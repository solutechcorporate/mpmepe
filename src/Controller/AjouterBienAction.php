<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Bien;
use App\Entity\BienCaracteristique;
use App\Entity\Caracteristique;
use App\Entity\DataBien;
use App\Entity\TypeBienAttribut;
use App\Repository\FilesRepository;
use App\Service\FileUploader;
use App\Service\RandomStringGeneratorServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class AjouterBienAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploader $fileUploader,
        private RandomStringGeneratorServices $randomStringGeneratorServices,
        private FilesRepository $filesRepository,
        private SerializerInterface $serializer,
        private SerializerContextBuilderInterface $serializerContextBuilder,
    )
    {
    }

    public function __invoke(Request $request): \ArrayObject
    {
        $data = new \ArrayObject([
            'message' => "Impossible de désérialiser les données."
        ]);

        if ($request->attributes->get('data') instanceof Bien) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Bien $bien */
            $bien = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $fichierUploades = $request->files->all();

                // Gestion des fichiers
                if ($fichierUploades !== null) {
                    // Enregistrement des images du bien
                    if (array_key_exists('images', $fichierUploades)) {
                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                        do {
                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                            $existFiles = $this->filesRepository->findBy([
                                'referenceCode' => $reference
                            ]);

                        } while (count($existFiles) > 0);

                        foreach ($fichierUploades['images'] as $fichier) {
                            if ($fichier instanceof UploadedFile) {
                                $this->fileUploader->saveFile(
                                    $fichier,
                                    false,
                                    Bien::class,
                                    null,
                                    $reference);
                            }
                        }

                        $bien->setImages($reference);
                    }

                    // Enregistrement des pièces jointes du bien
                    if (array_key_exists('pieceJointes', $fichierUploades)) {
                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                        do {
                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                            $existFiles = $this->filesRepository->findBy([
                                'referenceCode' => $reference
                            ]);

                        } while (count($existFiles) > 0);

                        foreach ($fichierUploades['pieceJointes'] as $fichier) {
                            if ($fichier instanceof UploadedFile) {
                                $this->fileUploader->saveFile(
                                    $fichier,
                                    false,
                                    Bien::class,
                                    null,
                                    $reference);
                            }
                        }

                        $bien->setPieceJointe($reference);
                    }

                } // Fin $fichierUploades !== null

                $this->entityManager->persist($bien);
                $this->entityManager->flush();
                $this->entityManager->refresh($bien);

                // Enregistrement des informations dans la table BienCaracteristique
                if (isset($request->request->all()['caracteristiques'])) {
                    foreach ($request->request->all()['caracteristiques'] as $caracteristiqueEntrypoint) {
                        if (trim($caracteristiqueEntrypoint) === '') {
                            continue;
                        }

                        $caracteristiqueId = explode('/', $caracteristiqueEntrypoint);
                        $caracteristiqueId = $caracteristiqueId[(count($caracteristiqueId) - 1)];

                        $caracteristiqueEntity = $this->entityManager
                            ->getRepository(Caracteristique::class)
                            ->find($caracteristiqueId)
                        ;

                        $bienCaracteristique = (new BienCaracteristique())
                            ->setBien($bien)
                            ->setCaracteristique($caracteristiqueEntity)
                            ->setUser($this->getUser())
                        ;

                        $this->entityManager->persist($bienCaracteristique);
                    }

                    $this->entityManager->flush();
                }

                // Enregistrement des informations dans la table DataBien
                if (isset($request->request->all()['attributSupplementaires'])) {
                    foreach ($request->request->all()['attributSupplementaires'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $dExplode = explode('^^^^^^^^^^', $d);

                        $typeBienAttributEntrypoint = $dExplode[0];
                        $value = $dExplode[1];

                        $typeBienAttributId = explode('/', $typeBienAttributEntrypoint);
                        $typeBienAttributId = $typeBienAttributId[(count($typeBienAttributId) - 1)];

                        $typeBienAttribut = $this->entityManager
                            ->getRepository(TypeBienAttribut::class)
                            ->find($typeBienAttributId)
                        ;

                        $dataBien = (new DataBien())
                            ->setBien($bien)
                            ->setTypeBienAttribut($typeBienAttribut)
                            ->setValue($value)
                            ->setUser($this->getUser())
                        ;

                        $this->entityManager->persist($dataBien);
                    }

                    $this->entityManager->flush();
                }

            } // resourceId n'existe pas

            // Modification des informations du bien
            if ($request->request->get('resourceId')) {
                $bien->setId((int) $request->request->get('resourceId'));

                $existBien = $this->entityManager->getRepository(Bien::class)
                    ->findOneBy(
                        [
                            'id' => $bien->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($bien, 'json', []);

                // Remplacement des valeurs dans $entitySerialise
                $entitySerialise = json_decode($entitySerialise, true);
                foreach ($entitySerialise as $k => $v) {
                    if (\gettype($v) === 'boolean') {
                        $entitySerialise[$k] = $v === true ? "1" : "0";
                    }

                    if (\gettype($v) === 'integer' || \gettype($v) === 'double') {
                        $entitySerialise[$k] = (string) $v;
                    }
                }
                $entitySerialise = json_encode($entitySerialise);

                if ($existBien) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existBien;
                    $bien = $this->serializer->deserialize($entitySerialise, Bien::class, 'json', $context);
                }

                // Gestion des fichiers
                if ($request->files->all() !== null) {
                    // Enregistrement ou modification des images du bien
                    if (array_key_exists('images', $request->files->all())) {
                        $reference = $bien->getImages();

                        if ($reference === null || trim($reference) === '') {
                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                            do {
                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                                $existFiles = $this->filesRepository->findBy([
                                    'referenceCode' => $reference
                                ]);

                            } while (count($existFiles) > 0);
                        }

                        foreach ($request->files->all()['images'] as $fichier) {
                            if ($fichier instanceof UploadedFile) {
                                $this->fileUploader->saveFile(
                                    $fichier,
                                    false,
                                    Bien::class,
                                    null,
                                    $reference
                                );
                            }
                        }

                        $bien->setImages($reference);
                    }

                    // Enregistrement ou modification des pièces jointes du bien
                    if (array_key_exists('pieceJointes', $request->files->all())) {
                        $reference = $bien->getPieceJointe();

                        if ($reference === null || trim($reference) === '') {
                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                            do {
                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                                $existFiles = $this->filesRepository->findBy([
                                    'referenceCode' => $reference
                                ]);

                            } while (count($existFiles) > 0);
                        }

                        foreach ($request->files->all()['pieceJointes'] as $fichier) {
                            if ($fichier instanceof UploadedFile) {
                                $this->fileUploader->saveFile(
                                    $fichier,
                                    false,
                                    Bien::class,
                                    null,
                                    $reference
                                );
                            }
                        }

                        $bien->setPieceJointe($reference);
                    }

                } // Fin gestion des fichiers

                $this->entityManager->flush();
                $this->entityManager->refresh($bien);

                // Enregistrement des informations dans la table BienCaracteristique
                if (isset($request->request->all()['caracteristiques'])) {
                    foreach ($request->request->all()['caracteristiques'] as $caracteristiqueEntrypoint) {
                        if (trim($caracteristiqueEntrypoint) === '') {
                            continue;
                        }

                        $caracteristiqueId = explode('/', $caracteristiqueEntrypoint);
                        $caracteristiqueId = $caracteristiqueId[(count($caracteristiqueId) - 1)];

                        $caracteristiqueEntity = $this->entityManager
                            ->getRepository(Caracteristique::class)
                            ->find($caracteristiqueId)
                        ;

                        $existBienCaracteristique = $this->entityManager
                            ->getRepository(BienCaracteristique::class)
                            ->findOneBy(
                                [
                                    'bien' => $bien,
                                    'caracteristique' => $caracteristiqueEntity
                                ]
                            )
                        ;

                        if ($existBienCaracteristique === null) {
                            $existBienCaracteristique = (new BienCaracteristique())
                                ->setBien($bien)
                                ->setCaracteristique($caracteristiqueEntity)
                                ->setUser($this->getUser())
                            ;

                            $this->entityManager->persist($existBienCaracteristique);
                        }
                    }

                    $this->entityManager->flush();
                }

                // Enregistrement des informations dans la table DataBien
                if (isset($request->request->all()['attributSupplementaires'])) {
                    foreach ($request->request->all()['attributSupplementaires'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $dExplode = explode('^^^^^^^^^^', $d);

                        $typeBienAttributEntrypoint = $dExplode[0];
                        $value = $dExplode[1];

                        $typeBienAttributId = explode('/', $typeBienAttributEntrypoint);
                        $typeBienAttributId = $typeBienAttributId[(count($typeBienAttributId) - 1)];

                        $typeBienAttribut = $this->entityManager
                            ->getRepository(TypeBienAttribut::class)
                            ->find($typeBienAttributId)
                        ;

                        $existDataBien = $this->entityManager
                            ->getRepository(DataBien::class)
                            ->findOneBy(
                                [
                                    'bien' => $bien,
                                    'typeBienAttribut' => $typeBienAttribut
                                ]
                            )
                        ;

                        if ($existDataBien) {
                            $existDataBien->setValue($value);

                        } else {
                            $existDataBien = (new DataBien())
                                ->setBien($bien)
                                ->setTypeBienAttribut($typeBienAttribut)
                                ->setValue($value)
                                ->setUser($this->getUser())
                            ;

                            $this->entityManager->persist($existDataBien);
                        }
                    }

                    $this->entityManager->flush();
                } // Fin enregistrement des données supplémentaires

            } // resourceId existe

            /*
             * Récupération des fichiers
             */
            $serverUrl = $this->getParameter('serverUrl');

            // Images du bien
            $fileImages = $this->filesRepository->findBy(
                [
                    'referenceCode' => $bien->getImages()
                ]
            );

            $tabFileImages = [];
            if (count($fileImages) > 0) {
                foreach ($fileImages as $fileImage) {
                    $tabFileImages[] = $serverUrl.$fileImage->getLocation().$fileImage->getFilename();
                }
            }

            // Pièces jointes du bien
            $filePieceJointes = $this->filesRepository->findBy(
                [
                    'referenceCode' => $bien->getPieceJointe()
                ]
            );

            $tabFilePieceJointes = [];
            if (count($filePieceJointes) > 0) {
                foreach ($filePieceJointes as $filePieceJointe) {
                    $tabFilePieceJointes[] = $serverUrl.$filePieceJointe->getLocation().$filePieceJointe->getFilename();
                }
            }

            // On retourne un objet ArrayObject
            // Decoder l'url encodé en JavaScript avec la fonction decodeURIComponent
            $data = new \ArrayObject([
                'bien' => $bien,
                'fichiers' => [
                    'images' => (count($tabFileImages) > 0) ? $tabFileImages : null,
                    'pieceJointes' => (count($tabFilePieceJointes) > 0) ? $tabFilePieceJointes : null,
                ]
            ]);
        }

        return $data;
    }

}
