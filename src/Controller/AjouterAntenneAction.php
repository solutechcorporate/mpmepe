<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Antenne;
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
final class AjouterAntenneAction extends AbstractController
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

        if ($request->attributes->get('data') instanceof Antenne) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Antenne $antenne */
            $antenne = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $fichierUploades = $request->files->all();

                // Gestion des fichiers
                if ($fichierUploades !== null) {
                    // Enregistrement du logo de l'antenne
                    if (array_key_exists('logo', $fichierUploades)) {
                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                        do {
                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                            $existFiles = $this->filesRepository->findBy([
                                'referenceCode' => $reference
                            ]);

                        } while (count($existFiles) > 0);

                        if ($fichierUploades['logo'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $fichierUploades['logo'],
                                false,
                                Antenne::class,
                                null,
                                $reference
                            );
                        }

                        $antenne->setLogo($reference);
                    }

                }

                $this->entityManager->persist($antenne);
                $this->entityManager->flush();
                $this->entityManager->refresh($antenne);

            } // resourceId n'existe pas

            // Modification des informations de l'antenne
            if ($request->request->get('resourceId')) {
                $antenne->setId((int) $request->request->get('resourceId'));

                $existAntenne = $this->entityManager->getRepository(Antenne::class)
                    ->findOneBy(
                        [
                            'id' => $antenne->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($antenne, 'json', []);

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

                if ($existAntenne) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existAntenne;
                    $antenne = $this->serializer->deserialize($entitySerialise, Antenne::class, 'json', $context);
                }

                // Gestion des fichiers
                if ($request->files->all() !== null) {
                    // Enregistrement ou modification du logo de l'antenne
                    if (array_key_exists('logo', $request->files->all())) {
                        $reference = $antenne->getLogo();

                        if ($reference === null || trim($reference) === '') {
                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
                            do {
                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);

                                $existFiles = $this->filesRepository->findBy([
                                    'referenceCode' => $reference
                                ]);

                            } while (count($existFiles) > 0);
                        }

                        if ($request->files->all()['logo'] instanceof UploadedFile) {
                            $this->fileUploader->saveFile(
                                $request->files->all()['logo'],
                                false,
                                Antenne::class,
                                $reference,
                                $reference
                            );
                        }

                        $antenne->setLogo($reference);
                    }

                }  // Fin gestion des fichiers

                $this->entityManager->flush();
                $this->entityManager->refresh($antenne);

            } // resourceId existe

            // Récupération des fichiers
            $fileLogo = $this->filesRepository->findOneBy(
                [
                    'referenceCode' => $antenne->getLogo()
                ]
            );

            $serverUrl = $this->getParameter('serverUrl');

            // On retourne un objet ArrayObject
            $data = new \ArrayObject([
                'antenne' => $antenne,
                'fichiers' => [
                    'logo' => $fileLogo ? $serverUrl.$fileLogo->getLocation().$fileLogo->getFilename() : null
                ]
            ]);
        }

        return $data;
    }

}
