<?php

namespace App\Controller;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use App\Entity\Header;
use App\Entity\Historique;
use App\Entity\Page;
use App\Entity\PageHeader;
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
final class AjouterHeaderAction extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private SerializerContextBuilderInterface $serializerContextBuilder,
    )
    {
    }

    public function __invoke(Request $request): object
    {
        $data = new \stdClass();
        $data->message = "Impossible de désérialiser les données.";

        if ($request->attributes->get('data') instanceof Header) {
            /*
            *  On traite ici l'enregistrement dans la base de données
            *  (équivaut à l'attribut de api operation:  write: false)
            */

            /** @var Header $header */
            $header = $request->attributes->get('data');

            // Nouvel enregistrement
            if (!$request->request->get('resourceId')) {
                $this->entityManager->persist($header);
                $this->entityManager->flush();
                $this->entityManager->refresh($header);

                // Enregistrement des données supplementaires
                if (isset($request->request->all()['page'])) {
                    foreach ($request->request->all()['page'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $pageId = explode('/', $d);
                        $pageId = $pageId[(count($pageId) - 1)];

                        $page = $this->entityManager
                            ->getRepository(Page::class)
                            ->find($pageId)
                        ;

                        $pageHeader = (new PageHeader())
                            ->setHeader($header)
                            ->setPage($page)
                        ;

                        $this->entityManager->persist($pageHeader);
                        $this->entityManager->flush();

                        // Gestion de nbLiaison et de l'historique
                        $header->setNbLiaison((int) $header->getNbLiaison() + 1);
                        $page->setNbLiaison((int) $page->getNbLiaison() + 1);

                        $historique = (new Historique())
                            ->setOperation("Ajout d'un nouvel enregistrement")
                            ->setNomTable("PageHeader")
                            ->setIdTable($pageHeader->getId())
                            ->setUser($this->getUser())
                        ;

                        $this->entityManager->persist($historique);
                    }

                    $this->entityManager->flush();
                } // Fin enregistrement des données supplémentaires

            } // resourceId n'existe pas

            // Modification des informations du header
            if ($request->request->get('resourceId')) {
                $header->setId((int) $request->request->get('resourceId'));

                $existHeader = $this->entityManager->getRepository(Header::class)
                    ->findOneBy(
                        [
                            'id' => $header->getId()
                        ]
                    )
                ;

                $attributes = RequestAttributesExtractor::extractAttributes($request);
                $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
                $entitySerialise = $this->serializer->serialize($header, 'json', []);

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

                if ($existHeader) {
                    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $existHeader;
                    $header = $this->serializer->deserialize($entitySerialise, Header::class, 'json', $context);
                }

                $this->entityManager->flush();
                $this->entityManager->refresh($header);

                // Enregistrement des données supplementaires
                if (isset($request->request->all()['page'])) {
                    foreach ($request->request->all()['page'] as $d) {
                        if (trim($d) === '') {
                            continue;
                        }

                        $pageId = explode('/', $d);
                        $pageId = $pageId[(count($pageId) - 1)];

                        $page = $this->entityManager
                            ->getRepository(Page::class)
                            ->find($pageId)
                        ;

                        $existPageHeader = $this->entityManager
                            ->getRepository(PageHeader::class)
                            ->findOneBy(
                                [
                                    'header' => $header,
                                    'page' => $page
                                ]
                            )
                        ;

                        if ($existPageHeader === null) {
                            $existPageHeader = (new PageHeader())
                                ->setHeader($header)
                                ->setPage($page)
                            ;

                            $this->entityManager->persist($existPageHeader);
                            $this->entityManager->flush();

                            // Gestion de nbLiaison et de l'historique
                            $header->setNbLiaison((int) $header->getNbLiaison() + 1);
                            $page->setNbLiaison((int) $page->getNbLiaison() + 1);

                            $historique = (new Historique())
                                ->setOperation("Ajout d'un nouvel enregistrement")
                                ->setNomTable("PageHeader")
                                ->setIdTable($existPageHeader->getId())
                                ->setUser($this->getUser())
                            ;

                            $this->entityManager->persist($historique);
                        }
                    }

                    $this->entityManager->flush();
                } // Fin enregistrement des données supplémentaires

            } // resourceId existe

            // On retourne un objet ArrayObject
            $data = $header;
        }

        return $data;
    }

}
