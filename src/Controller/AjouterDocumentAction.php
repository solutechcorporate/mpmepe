<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Document;
use App\Entity\DocumentCategorieDocument;
use App\Entity\CategorieDocument;
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
final class AjouterDocumentAction extends AbstractController
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

        if ($request->attributes->get('data') instanceof Document) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Document $document */
            $document = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $fichierUploades = $request->files->all();

                // Gestion des fichiers
                if ($fichierUploades !== null) {
                    // Enregistrement du document
                    if (array_key_exists('docFichier', $fichierUploades)) {
                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                        do {
                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                            $existFiles = $this->filesRepository->findBy([
                                'referenceCode' => $reference
                            ]);

                        } while (count($existFiles) > 0);

                        $fileObj = null;

                        if ($fichierUploades['docFichier'] instanceof UploadedFile) {
                            $fileObj = $this->fileUploader->saveFile(
                                $fichierUploades['docFichier'],
                                false,
                                Document::class,
                                null,
                                $reference,
                                true
                            );
                        }

                        $document->setDocCodeFichier($reference);
                        $document->setTailleFichier($fileObj !== null ? $fileObj->getSize() : $fileObj);
                        $document->setExtensionFichier($fileObj !== null ? strtoupper($fileObj->getType()) : $fileObj);
                    }

                }

                $this->entityManager->persist($document);
                $this->entityManager->flush();
                $this->entityManager->refresh($document);

                // Enregistrement des DocumentCategorieDocument
                if (isset($request->request->all()['categorieDocument'])) {
                    foreach ($request->request->all()['categorieDocument'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $categorieDocumentId = explode('/', $d);
                        $categorieDocumentId = $categorieDocumentId[(count($categorieDocumentId) - 1)];

                        $categorieDocument = $this->entityManager
                            ->getRepository(CategorieDocument::class)
                            ->find($categorieDocumentId)
                        ;

                        $documentCategorieDocument = (new DocumentCategorieDocument())
                            ->setDocument($document)
                            ->setCategorieDocument($categorieDocument)
                        ;

                        $this->entityManager->persist($documentCategorieDocument);
                    }

                    $this->entityManager->flush();
                } // Fin enregistrement des DocumentCategorieDocument

            } // resourceId n'existe pas

            // Modification des informations du document
            if ($request->request->get('resourceId')) {
                $document->setId((int) $request->request->get('resourceId'));

                $existDocument = $this->entityManager->getRepository(Document::class)
                    ->findOneBy(
                        [
                            'id' => $document->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($document, 'json', []);

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

                if ($existDocument) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existDocument;
                    $document = $this->serializer->deserialize($entitySerialise, Document::class, 'json', $context);
                }

                // Gestion des fichiers
                if ($request->files->all() !== null) {
                    // Enregistrement ou modification du document
                    if (array_key_exists('docFichier', $request->files->all())) {
                        $reference = $document->getDocCodeFichier();

                        if ($reference === null || trim($reference) === '') {
                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                            do {
                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                                $existFiles = $this->filesRepository->findBy([
                                    'referenceCode' => $reference
                                ]);

                            } while (count($existFiles) > 0);
                        }

                        $fileObj = null;

                        if ($request->files->all()['docFichier'] instanceof UploadedFile) {
                            $fileObj = $this->fileUploader->saveFile(
                                $request->files->all()['docFichier'],
                                false,
                                Document::class,
                                $reference,
                                $reference,
                                true
                            );
                        }

                        $document->setDocCodeFichier($reference);
                        $document->setTailleFichier($fileObj !== null ? $fileObj->getSize() : $fileObj);
                        $document->setExtensionFichier($fileObj !== null ? strtoupper($fileObj->getType()) : $fileObj);
                    }

                }  // Fin gestion des fichiers

                $this->entityManager->flush();
                $this->entityManager->refresh($document);

                // Enregistrement des DocumentCategorieDocument
                if (isset($request->request->all()['categorieDocument'])) {
                    foreach ($request->request->all()['categorieDocument'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $categorieDocumentId = explode('/', $d);
                        $categorieDocumentId = $categorieDocumentId[(count($categorieDocumentId) - 1)];

                        $categorieDocument = $this->entityManager
                            ->getRepository(CategorieDocument::class)
                            ->find($categorieDocumentId)
                        ;

                        $existDocumentCategorieDocument = $this->entityManager
                            ->getRepository(DocumentCategorieDocument::class)
                            ->findOneBy(
                                [
                                    'document' => $document,
                                    'categorieDocument' => $categorieDocument
                                ]
                            )
                        ;

                        if ($existDocumentCategorieDocument === null) {
                            $existDocumentCategorieDocument = (new DocumentCategorieDocument())
                                ->setDocument($document)
                                ->setCategorieDocument($categorieDocument)
                            ;

                            $this->entityManager->persist($existDocumentCategorieDocument);
                        }
                    }

                    $this->entityManager->flush();
                } // Fin enregistrement des DocumentCategorieDocument

            } // resourceId existe

            // Récupération des fichiers
            $fileDoc = $this->filesRepository->findOneBy(
                [
                    'referenceCode' => $document->getDocCodeFichier()
                ]
            );

            $serverUrl = $this->getParameter('serverUrl');

            $fichiers = [
                'docFichier' => $fileDoc ? $serverUrl.$fileDoc->getLocation().$fileDoc->getFilename() : null
            ];
            $document->setFichiers($fichiers);

            // On retourne un objet ArrayObject
            $data = $document;
        }

        return $data;
    }

}
