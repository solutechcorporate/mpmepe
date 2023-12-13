<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Service;
use App\Repository\FilesRepository;
use App\Service\DeserializeApiPlatformResource;
use App\Service\FileUploader;
use App\Service\RandomStringGeneratorServices;
use App\Service\ValidateApiPlatformResource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class AjouterServiceAction extends AbstractController
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

        if ($request->attributes->get('data') instanceof Service) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Service $service */
            $service = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $fichierUploades = $request->files->all();

                // Gestion des fichiers
                if ($fichierUploades !== null) {
                    // Enregistrement de l'image d'entête de l'article
                    if (array_key_exists('imageEntete', $fichierUploades)) {
                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                        do {
                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                            $existFiles = $this->filesRepository->findBy([
                                'referenceCode' => $reference
                            ]);

                        } while (count($existFiles) > 0);

                        if ($fichierUploades['imageEntete'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $fichierUploades['imageEntete'],
                                false,
                                Service::class,
                                null,
                                $reference
                            );
                        }

                        $service->setCodeFichierImageEntete($reference);
                    }
                }

                $this->entityManager->persist($service);
                $this->entityManager->flush();
                $this->entityManager->refresh($service);

            } // resourceId n'existe pas

            // Modification des informations du service
            if ($request->request->get('resourceId')) {
                $service->setId((int) $request->request->get('resourceId'));

                $existService = $this->entityManager->getRepository(Service::class)
                    ->findOneBy(
                        [
                            'id' => $service->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($service, 'json', []);

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

                if ($existService) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existService;
                    $service = $this->serializer->deserialize($entitySerialise, Service::class, 'json', $context);
                }

                // Gestion des fichiers
                if ($request->files->all() !== null) {
                    // Enregistrement ou modification de l'image d'entête du service
                    if (array_key_exists('imageEntete', $request->files->all())) {
                        $reference = $service->getCodeFichierImageEntete();

                        if ($reference === null || trim($reference) === '') {
                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                            do {
                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                                $existFiles = $this->filesRepository->findBy([
                                    'referenceCode' => $reference
                                ]);

                            } while (count($existFiles) > 0);
                        }

                        if ($request->files->all()['imageEntete'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $request->files->all()['imageEntete'],
                                false,
                                Service::class,
                                $reference,
                                $reference
                            );
                        }

                        $service->setCodeFichierImageEntete($reference);
                    }

                }  // Fin gestion des fichiers

                $this->entityManager->flush();
                $this->entityManager->refresh($service);

            } // resourceId existe

            // Récupération des fichiers
            $fileImageEntete = $this->filesRepository->findOneBy(
                [
                    'referenceCode' => $service->getCodeFichierImageEntete()
                ]
            );

            $serverUrl = $this->getParameter('serverUrl');

            // On retourne un objet ArrayObject
            // Decoder l'url encodé en JavaScript avec la fonction decodeURIComponent
            $data = new \ArrayObject([
                'service' => $service,
                'fichiers' => [
                    'imageEntete' => $fileImageEntete ? urlencode($serverUrl.$fileImageEntete->getLocation().$fileImageEntete->getFilename()) : null
                ]
            ]);
        }

        return $data;
    }

}
