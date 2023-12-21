<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Ministere;
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
final class AjouterMinistereAction extends AbstractController
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

    public function __invoke(Request $request): object
    {
        $data = new \stdClass();
        $data->message = "Impossible de désérialiser les données.";

        if ($request->attributes->get('data') instanceof Ministere) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Ministere $ministere */
            $ministere = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $fichierUploades = $request->files->all();

                // Gestion des fichiers
                if ($fichierUploades !== null) {
                    // Enregistrement du logo du ministere
                    if (array_key_exists('logoFichier', $fichierUploades)) {
                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                        do {
                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                            $existFiles = $this->filesRepository->findBy([
                                'referenceCode' => $reference
                            ]);

                        } while (count($existFiles) > 0);

                        if ($fichierUploades['logoFichier'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $fichierUploades['logoFichier'],
                                false,
                                Ministere::class,
                                null,
                                $reference
                            );
                        }

                        $ministere->setLogoCodeFichier($reference);
                    }

                }

                $this->entityManager->persist($ministere);
                $this->entityManager->flush();
                $this->entityManager->refresh($ministere);

            } // resourceId n'existe pas

            // Modification des informations du ministere
            if ($request->request->get('resourceId')) {
                $ministere->setId((int) $request->request->get('resourceId'));

                $existMinistere = $this->entityManager->getRepository(Ministere::class)
                    ->findOneBy(
                        [
                            'id' => $ministere->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($ministere, 'json', []);

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

                if ($existMinistere) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existMinistere;
                    $ministere = $this->serializer->deserialize($entitySerialise, Ministere::class, 'json', $context);
                }

                // Gestion des fichiers
                if ($request->files->all() !== null) {
                    // Enregistrement ou modification du logo du ministere
                    if (array_key_exists('logoFichier', $request->files->all())) {
                        $reference = $ministere->getLogoCodeFichier();

                        if ($reference === null || trim($reference) === '') {
                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                            do {
                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                                $existFiles = $this->filesRepository->findBy([
                                    'referenceCode' => $reference
                                ]);

                            } while (count($existFiles) > 0);
                        }

                        if ($request->files->all()['logoFichier'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $request->files->all()['logoFichier'],
                                false,
                                Ministere::class,
                                $reference,
                                $reference
                            );
                        }

                        $ministere->setLogoCodeFichier($reference);
                    }

                }  // Fin gestion des fichiers

                $this->entityManager->flush();
                $this->entityManager->refresh($ministere);

            } // resourceId existe

            // Récupération des fichiers
            $fileLogo = $this->filesRepository->findOneBy(
                [
                    'referenceCode' => $ministere->getLogoCodeFichier()
                ]
            );

            $serverUrl = $this->getParameter('serverUrl');

            $fichiers = [
                'logoFichier' => $fileLogo ? $serverUrl.$fileLogo->getLocation().$fileLogo->getFilename() : null
            ];
            $ministere->setFichiers($fichiers);

            // On retourne un objet ArrayObject
            $data = $ministere;
        }

        return $data;
    }

}
