<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\Role;
use App\Repository\FilesRepository;
use App\Service\FileUploader;
use App\Service\RandomStringGeneratorServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class AjouterUserAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
//        private FileUploader $fileUploader,
//        private RandomStringGeneratorServices $randomStringGeneratorServices,
//        private FilesRepository $filesRepository,
        private UserPasswordHasherInterface $passwordHasher,
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

        if ($request->attributes->get('data') instanceof User) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var User $user */
            $user = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                if ($user->getPlainPassword() && trim($user->getPlainPassword()) !== '') {
                    $hashedPassword = $this->passwordHasher->hashPassword(
                        $user,
                        $user->getPlainPassword()
                    );

                    $user->setPassword($hashedPassword);
                    $user->eraseCredentials();
                }

                $user->setRoles(['ROLE_ADMIN']);

//                if ($user->getTypeUser() instanceof TypeUser) {
//                    $user->setRoles(
//                        $this->getRoleUser($user->getTypeUser())
//                    );
//                }

//                $fichierUploades = $request->files->all();
//
//                // Gestion des fichiers
//                if ($fichierUploades !== null) {
//                    // Enregistrement de la photo de l'utilisateur
//                    if (array_key_exists('photo', $fichierUploades)) {
//                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
//                        do {
//                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);
//
//                            $existFiles = $this->filesRepository->findBy([
//                                'referenceCode' => $reference
//                            ]);
//
//                        } while (count($existFiles) > 0);
//
//                        if ($fichierUploades['photo'] instanceof UploadedFile) {
//                            $this->fileUploader->saveFile(
//                                $fichierUploades['photo'],
//                                false,
//                                User::class,
//                                null,
//                                $reference);
//                        }
//
//                        $user->setPhotoCodeFichier($reference);
//                    }
//
//                    // Enregistrement du selfiePiece
//                    if (array_key_exists('selfiePiece', $fichierUploades)) {
//                        // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
//                        do {
//                            $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);
//
//                            $existFiles = $this->filesRepository->findBy([
//                                'referenceCode' => $reference
//                            ]);
//
//                        } while (count($existFiles) > 0);
//
//                        if ($fichierUploades['selfiePiece'] instanceof UploadedFile) {
//                            $this->fileUploader->saveFile(
//                                $fichierUploades['selfiePiece'],
//                                false,
//                                User::class,
//                                null,
//                                $reference
//                            );
//                        }
//
//                        $user->setSelfiePieceCodeFichier($reference);
//                    }
//
//                }

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->entityManager->refresh($user);

                // Enregistrement des rôles
                if (isset($request->request->all()['role'])) {
                    foreach ($request->request->all()['role'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $roleId = explode('/', $d);
                        $roleId = $roleId[(count($roleId) - 1)];

                        $role = $this->entityManager
                            ->getRepository(Role::class)
                            ->find($roleId)
                        ;

                        $userRole = (new UserRole())
                            ->setUser($user)
                            ->setRole($role)
                        ;

                        $this->entityManager->persist($userRole);
                    }

                    $this->entityManager->flush();
                } // Fin enregistrement des rôles

            } // resourceId n'existe pas

            // Modification des informations de l'utilisateur
            if ($request->request->get('resourceId')) {
                $user->setId((int) $request->request->get('resourceId'));

                $existUser = $this->entityManager->getRepository(User::class)
                    ->findOneBy(
                        [
                            'id' => $user->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($user, 'json', []);

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

                if ($existUser) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existUser;
                    $user = $this->serializer->deserialize($entitySerialise, User::class, 'json', $context);
                }

                if ($user->getPlainPassword() && trim($user->getPlainPassword()) !== '') {
                    $hashedPassword = $this->passwordHasher->hashPassword(
                        $user,
                        $user->getPlainPassword()
                    );

                    $user->setPassword($hashedPassword);
                    $user->eraseCredentials();
                }

                $user->setRoles(['ROLE_ADMIN']);
                
//                if ($user->getTypeUser() instanceof TypeUser) {
//                    $user->setRoles(
//                        $this->getRoleUser($user->getTypeUser())
//                    );
//                }

                // Gestion des fichiers
//                if ($request->files->all() !== null) {
//                    // Enregistrement ou modification de la photo de l'utilisateur
//                    if (array_key_exists('photo', $request->files->all())) {
//                        $reference = $user->getPhotoCodeFichier();
//
//                        if ($reference === null || trim($reference) === '') {
//                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
//                            do {
//                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);
//
//                                $existFiles = $this->filesRepository->findBy([
//                                    'referenceCode' => $reference
//                                ]);
//
//                            } while (count($existFiles) > 0);
//                        }
//
//                        if ($request->files->all()['photo'] instanceof UploadedFile) {
//                            $this->fileUploader->saveFile(
//                                $request->files->all()['photo'],
//                                false,
//                                User::class,
//                                $reference,
//                                $reference
//                            );
//                        }
//
//                        $user->setPhotoCodeFichier($reference);
//                    }
//
//                    // Enregistrement du selfiePiece
//                    if (array_key_exists('selfiePiece', $request->files->all())) {
//                        $reference = $user->getSelfiePieceCodeFichier();
//
//                        if ($reference === null || trim($reference) === '') {
//                            // On s'assure que la reference est unique pour ne pas lier d'autres fichiers
//                            do {
//                                $reference = $this->randomStringGeneratorServices->random_alphanumeric(16);
//
//                                $existFiles = $this->filesRepository->findBy([
//                                    'referenceCode' => $reference
//                                ]);
//
//                            } while (count($existFiles) > 0);
//                        }
//
//                        if ($request->files->all()['selfiePiece'] instanceof UploadedFile) {
//                            $this->fileUploader->saveFile(
//                                $request->files->all()['selfiePiece'],
//                                false,
//                                User::class,
//                                $reference,
//                                $reference
//                            );
//                        }
//
//                        $user->setSelfiePieceCodeFichier($reference);
//                    }
//
//                }

                $this->entityManager->flush();
                $this->entityManager->refresh($user);

                // Enregistrement des rôles
                if (isset($request->request->all()['role'])) {
                    foreach ($request->request->all()['role'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $roleId = explode('/', $d);
                        $roleId = $roleId[(count($roleId) - 1)];

                        $role = $this->entityManager
                            ->getRepository(Role::class)
                            ->find($roleId)
                        ;

                        $existUserRole = $this->entityManager
                            ->getRepository(UserRole::class)
                            ->findOneBy(
                                [
                                    'user' => $user,
                                    'role' => $role
                                ]
                            )
                        ;

                        if ($existUserRole === null) {
                            $existUserRole = (new UserRole())
                                ->setUser($user)
                                ->setRole($role)
                            ;

                            $this->entityManager->persist($existUserRole);
                        }
                    }

                    $this->entityManager->flush();
                } // Fin enregistrement des rôles

            } // resourceId existe

            // Récupération des fichiers
//            $filePhotoCodeFichier = $this->filesRepository->findOneBy(
//                [
//                    'referenceCode' => $user->getPhotoCodeFichier()
//                ]
//            );

//            $fileSelfiePieceCodeFichier = $this->filesRepository->findOneBy(
//                [
//                    'referenceCode' => $user->getSelfiePieceCodeFichier()
//                ]
//            );

//            $serverUrl = $this->getParameter('serverUrl');
//
//            $fichiers = [
//                'photo' => $filePhotoCodeFichier ? $serverUrl.$filePhotoCodeFichier->getLocation().$filePhotoCodeFichier->getFilename() : null,
//                'selfiePiece' => $fileSelfiePieceCodeFichier ? $serverUrl.$fileSelfiePieceCodeFichier->getLocation().$fileSelfiePieceCodeFichier->getFilename() : null
//            ];
//            $user->setFichiers($fichiers);

            // On retourne un objet ArrayObject
            $data = new \ArrayObject([
                'user' => $user,
            ]);
        }

        return $data;
    }

//    private function getRoleUser(TypeUser $typeUser): array
//    {
//        $nomType = $typeUser->getNom();
//
//        switch ($nomType) {
//            case "Agent immobilier":
//                return ['ROLE_AGENT_IMMO'];
//
//            case "Chef d'agence":
//                return ['ROLE_CHEF_AGENCE'];
//
//            case "Démarcheur":
//                return ['ROLE_DEMARCHEUR'];
//
//            case "Client":
//                return ['ROLE_CLIENT'];
//
//            case "Propriétaire":
//                return ['ROLE_PROPRIETAIRE'];
//
//            case "Administrateur":
//                return ['ROLE_ADMIN'];
//
//            case "Conseiller":
//                return ['ROLE_CONSEILLER'];
//
//            default:
//                return [];
//        }
//    }

}
