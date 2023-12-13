<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Agence;
use App\Entity\DataUser;
use App\Entity\TypeUser;
use App\Entity\TypeUserAttribut;
use App\Entity\User;
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
final class AjouterAgenceAction extends AbstractController
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

        if ($request->attributes->get('data') instanceof Agence) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Agence $agence */
            $agence = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $fichierUploades = $request->files->all();

                // Gestion des fichiers
                if ($fichierUploades !== null) {
                    // Enregistrement du logo de l'agence
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
                                Agence::class,
                                null,
                                $reference);
                        }

                        $agence->setLogo($reference);
                    }

                }

                $this->entityManager->persist($agence);
                $this->entityManager->flush();
                $this->entityManager->refresh($agence);

            } // resourceId n'existe pas

            // Modification des informations de l'agence
            if ($request->request->get('resourceId')) {
                $agence->setId((int) $request->request->get('resourceId'));

                $existAgence = $this->entityManager->getRepository(Agence::class)
                    ->findOneBy(
                        [
                            'id' => $agence->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($agence, 'json', []);

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

                if ($existAgence) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existAgence;
                    $agence = $this->serializer->deserialize($entitySerialise, Agence::class, 'json', $context);
                }

                // Gestion des fichiers
                if ($request->files->all() !== null) {
                    // Enregistrement ou modification du logo de l'agence
                    if (array_key_exists('logo', $request->files->all())) {
                        $reference = $agence->getLogo();

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
                                Agence::class,
                                $reference,
                                $reference
                            );
                        }

                        $agence->setLogo($reference);
                    }

                }  // Fin gestion des fichiers

                $this->entityManager->flush();
                $this->entityManager->refresh($agence);

            } // resourceId existe

            // Récupération des fichiers
            $fileLogo = $this->filesRepository->findOneBy(
                [
                    'referenceCode' => $agence->getLogo()
                ]
            );

            $serverUrl = $this->getParameter('serverUrl');

            // On retourne un objet ArrayObject
            // Decoder l'url encodé en JavaScript avec la fonction decodeURIComponent
            $data = new \ArrayObject([
                'agence' => $agence,
                'fichiers' => [
                    'logo' => $fileLogo ? $serverUrl.$fileLogo->getLocation().$fileLogo->getFilename() : null
                ]
            ]);
        }

        return $data;
    }

}
