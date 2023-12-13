<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Pays;
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
final class AjouterPaysAction extends AbstractController
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

        if ($request->attributes->get('data') instanceof Pays) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Pays $pays */
            $pays = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $fichierUploades = $request->files->all();

                // Gestion des fichiers
                if ($fichierUploades !== null) {
                    // Enregistrement du drapeau du pays
                    if (array_key_exists('drapeau', $fichierUploades)) {
                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                        do {
                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                            $existFiles = $this->filesRepository->findBy([
                                'referenceCode' => $reference
                            ]);

                        } while (count($existFiles) > 0);

                        if ($fichierUploades['drapeau'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $fichierUploades['drapeau'],
                                false,
                                Pays::class,
                                null,
                                $reference);
                        }

                        $pays->setDrapeau($reference);
                    }

                }

                $this->entityManager->persist($pays);
                $this->entityManager->flush();
                $this->entityManager->refresh($pays);

            } // resourceId n'existe pas

            // Modification des informations du pays
            if ($request->request->get('resourceId')) {
                $pays->setId((int) $request->request->get('resourceId'));

                $existPays = $this->entityManager->getRepository(Pays::class)
                    ->findOneBy(
                        [
                            'id' => $pays->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($pays, 'json', []);

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

                if ($existPays) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existPays;
                    $pays = $this->serializer->deserialize($entitySerialise, Pays::class, 'json', $context);
                }

                // Gestion des fichiers
                if ($request->files->all() !== null) {
                    // Enregistrement ou modification du drapeau du pays
                    if (array_key_exists('drapeau', $request->files->all())) {
                        $reference = $pays->getDrapeau();

                        if ($reference === null || trim($reference) === '') {
                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                            do {
                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                                $existFiles = $this->filesRepository->findBy([
                                    'referenceCode' => $reference
                                ]);

                            } while (count($existFiles) > 0);
                        }

                        if ($request->files->all()['drapeau'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $request->files->all()['drapeau'],
                                false,
                                Pays::class,
                                $reference,
                                $reference
                            );
                        }

                        $pays->setDrapeau($reference);
                    }

                } // Fin gestion des fichiers

                $this->entityManager->flush();
                $this->entityManager->refresh($pays);

            } // resourceId existe

            // Récupération des fichiers
            $fileDrapeau = $this->filesRepository->findOneBy(
                [
                    'referenceCode' => $pays->getDrapeau()
                ]
            );

            $serverUrl = $this->getParameter('serverUrl');

            // On retourne un objet ArrayObject
            // Decoder l'url encodé en JavaScript avec la fonction decodeURIComponent
            $data = new \ArrayObject([
                'pays' => $pays,
                'fichiers' => [
                    'drapeau' => $fileDrapeau ? $serverUrl.$fileDrapeau->getLocation().$fileDrapeau->getFilename() : null
                ]
            ]);
        }

        return $data;
    }

}
