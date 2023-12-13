<?php

namespace App\Service;

use App\Entity\Files;
use App\Repository\FilesRepository;
use App\Service\GeneraleServices;
use App\Service\RandomStringGeneratorServices;
use App\Utils\Constants\AppConstants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private SluggerInterface $slugger,
        private EntityManagerInterface $entityManager,
        private FilesRepository $filesRepository,
        private GeneraleServices $generaleServices,
        private RandomStringGeneratorServices $randomStringGeneratorServices
    )
    {
    }

    public function saveFile(
        UploadedFile $file,
        $temp = false,
        $entityClass = null,
        $existFileCode = null,
        $reference = null,
        $returnFileObj = false,
        $sendByEmail = false,
    )
    {
        // Fichiers à envoyer par mail
        if ($sendByEmail) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            // Get folder location based on entity
            $location = $this->getFileFolderDependOnEntity(null);

            try {
                $file->move($location, $fileName);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            return dirname(__FILE__, 3) . '/public/' . $location . $fileName;
        }

        // Remove file from DB & FileFolder if exist
        if ($existFileCode !== null) {
            $existFiles = $this->filesRepository->findBy([
                'referenceCode' => $existFileCode,
            ]);

            foreach ($existFiles as $existFile) {
                try {
                    unlink(dirname(__FILE__, 3) . '/public/' . $existFile->getLocation() . $existFile->getFilename());
                } catch (\Exception) {
                }
                $this->filesRepository->remove($existFile);
            }
        }

        // Create and save new file
        if ($reference === null) {
            $reference = $this->randomStringGeneratorServices->random_alphanumeric_custom_length(16);
        }

        $extension = $file->getClientOriginalExtension();
        $fileNewName = md5(uniqid()) . '.' . $extension;

        // Get folder location based on entity
        $location = $this->getFileFolderDependOnEntity(
            $this->generaleServices->getTableName($entityClass)
        );

        try {
            $file->move($location, $fileNewName);
        } catch (\Exception) {
        }

        $file = new Files();
        $file->setFilename($fileNewName);
        $file->setTemp($temp);
        $file->setSize(0);
        $file->setLocation($location);
        $file->setType($extension);
        $file->setReferenceCode($reference);
        $this->entityManager->persist($file);

        if ($returnFileObj) {
            return $file;
        }
        return $reference;
    }

    private function getFileFolderDependOnEntity($entityClass)
    {
        switch ($entityClass) {
            case 'article':
                return AppConstants::ARTICLE_FOLDER;
            case 'ministere':
                return AppConstants::MINISTERE_FOLDER;
            case 'social_network':
                return AppConstants::SOCIAL_NETWORK_FOLDER;
            case 'document':
                return AppConstants::DOCUMENT_FOLDER;
            case 'page':
                return AppConstants::PAGE_FOLDER;
            case 'user':
                return AppConstants::USER_FOLDER;
            default:
                return AppConstants::DEFAULT_FOLDER;
        }
    }

    public function getFilesByFileCode(
        string $filesCode,
        string $returnSingleFile = '0'
    )
    {
        if ($returnSingleFile === '1') {
            $singleFile = $this->filesRepository->findOneBy([
                'referenceCode' => $filesCode,
            ]);
            if ($singleFile) {
                return $singleFile->getLocation() . $singleFile->getFilename();
            }
            return $filesCode;
        }

        return $this->filesRepository->findBy([
            'referenceCode' => $filesCode,
        ]);
    }

}
