<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Page;
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
final class AjouterPageAction extends AbstractController
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

        if ($request->attributes->get('data') instanceof Page) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Page $page */
            $page = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $fichierUploades = $request->files->all();

                // Gestion des fichiers
                if ($fichierUploades !== null) {
                    // Enregistrement de l'image de la page
                    if (array_key_exists('imageFichier', $fichierUploades)) {
                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                        do {
                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                            $existFiles = $this->filesRepository->findBy([
                                'referenceCode' => $reference
                            ]);

                        } while (count($existFiles) > 0);

                        if ($fichierUploades['imageFichier'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $fichierUploades['imageFichier'],
                                false,
                                Page::class,
                                null,
                                $reference
                            );
                        }

                        $page->setImageCodeFichier($reference);
                    }

                }

                $this->entityManager->persist($page);
                $this->entityManager->flush();
                $this->entityManager->refresh($page);

            } // resourceId n'existe pas

            // Modification des informations de la page
            if ($request->request->get('resourceId')) {
                $page->setId((int) $request->request->get('resourceId'));

                $existPage = $this->entityManager->getRepository(Page::class)
                    ->findOneBy(
                        [
                            'id' => $page->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($page, 'json', []);

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

                if ($existPage) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existPage;
                    $page = $this->serializer->deserialize($entitySerialise, Page::class, 'json', $context);
                }

                // Gestion des fichiers
                if ($request->files->all() !== null) {
                    // Enregistrement ou modification de l'image de la page
                    if (array_key_exists('imageFichier', $request->files->all())) {
                        $reference = $page->getImageCodeFichier();

                        if ($reference === null || trim($reference) === '') {
                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                            do {
                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                                $existFiles = $this->filesRepository->findBy([
                                    'referenceCode' => $reference
                                ]);

                            } while (count($existFiles) > 0);
                        }

                        if ($request->files->all()['imageFichier'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $request->files->all()['imageFichier'],
                                false,
                                Page::class,
                                $reference,
                                $reference
                            );
                        }

                        $page->setImageCodeFichier($reference);
                    }

                }  // Fin gestion des fichiers

                $this->entityManager->flush();
                $this->entityManager->refresh($page);

            } // resourceId existe

            // Récupération des fichiers
            $fileImage = $this->filesRepository->findOneBy(
                [
                    'referenceCode' => $page->getImageCodeFichier()
                ]
            );

            $serverUrl = $this->getParameter('serverUrl');
            
            $fichiers = [
                'imageFichier' => $fileImage ? $serverUrl.$fileImage->getLocation().$fileImage->getFilename() : null
            ];
            $page->setFichiers($fichiers);

            // On retourne un objet ArrayObject
            $data = new \ArrayObject([
                'page' => $page,
            ]);
        }

        return $data;
    }

}
