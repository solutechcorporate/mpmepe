<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\ArticleConseil;
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
final class AjouterArticleConseilAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploader $fileUploader,
        private RandomStringGeneratorServices $randomStringGeneratorServices,
        private FilesRepository $filesRepository,
        private SerializerInterface $serializer,
        private SerializerContextBuilderInterface $serializerContextBuilder
    )
    {
    }

    public function __invoke(Request $request): \ArrayObject
    {
        $data = new \ArrayObject([
            'message' => "Impossible de désérialiser les données."
        ]);

        if ($request->attributes->get('data') instanceof ArticleConseil) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var ArticleConseil $articleConseil */
            $articleConseil = $request->attributes->get('data');

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
                                ArticleConseil::class,
                                null,
                                $reference
                            );
                        }

                        $articleConseil->setCodeFichierImageEntete($reference);
                    }
                }

                $this->entityManager->persist($articleConseil);
                $this->entityManager->flush();
                $this->entityManager->refresh($articleConseil);

            } // resourceId n'existe pas

            // Modification des informations de l'article
            if ($request->request->get('resourceId')) {
                $articleConseil->setId((int) $request->request->get('resourceId'));

                $existArticleConseil = $this->entityManager->getRepository(ArticleConseil::class)
                    ->findOneBy(
                        [
                            'id' => $articleConseil->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($articleConseil, 'json', []);

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

                if ($existArticleConseil) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existArticleConseil;
                    $articleConseil = $this->serializer->deserialize($entitySerialise, ArticleConseil::class, 'json', $context);
                }

                // Gestion des fichiers
                if ($request->files->all() !== null) {
                    // Enregistrement ou modification de l'image d'entête de l'article
                    if (array_key_exists('imageEntete', $request->files->all())) {
                        $reference = $articleConseil->getCodeFichierImageEntete();

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
                                ArticleConseil::class,
                                $reference,
                                $reference
                            );
                        }

                        $articleConseil->setCodeFichierImageEntete($reference);
                    }

                }  // Fin gestion des fichiers

                $this->entityManager->flush();
                $this->entityManager->refresh($articleConseil);

            } // resourceId existe

            // Récupération des fichiers
            $fileImageEntete = $this->filesRepository->findOneBy(
                [
                    'referenceCode' => $articleConseil->getCodeFichierImageEntete()
                ]
            );

            $serverUrl = $this->getParameter('serverUrl');

            // On retourne un objet ArrayObject
            $data = new \ArrayObject([
                'articleConseil' => $articleConseil,
                'fichiers' => [
                    'imageEntete' => $fileImageEntete ? $serverUrl.$fileImageEntete->getLocation().$fileImageEntete->getFilename() : null
                ]
            ]);
        }

        return $data;
    }

}
