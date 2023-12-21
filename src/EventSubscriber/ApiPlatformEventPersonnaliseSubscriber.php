<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiPlatformEventPersonnaliseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                ['insertUserAjout', EventPriorities::PRE_WRITE],
                ['writeHistorique', EventPriorities::POST_WRITE]
            ]
        ];
    }

    public function insertUserAjout(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $request = $event->getRequest();
        $method = $request->getMethod();

        if ($method !== Request::METHOD_POST) {
            return;
        }

        $reflectionClass = new \ReflectionClass($entity::class);
        $userAjoutSet = $reflectionClass->hasProperty('userAjout');

        if ($userAjoutSet === true) {
            if (!$request->request->get('resourceId')) {
                // Cas d'un ajout
                $entity->setUserAjout($this->security->getUser());
                $event->setControllerResult($entity);
            }
        }
    }

    public function writeHistorique(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $method = $request->getMethod();

        if ($method === Request::METHOD_GET) {
            return;
        }

        $entity = $event->getControllerResult();

        $nomTable = explode('\\', $entity::class);
        $nomTable = $nomTable[(count($nomTable) - 1)];

        $idTable = $entity->getId();

        if ($method === Request::METHOD_POST) {
            if (!$request->request->get('resourceId')) {
                // Cas d'un ajout
                $historique = (new Historique())
                    ->setOperation("Ajout d'un nouvel enregistrement")
                    ->setNomTable($nomTable)
                    ->setIdTable($idTable)
                    ->setUser($this->security->getUser())
                ;

                $this->entityManager->persist($historique);
                $this->entityManager->flush();
            } else {
                // Cas d'une modification
                $historique = (new Historique())
                    ->setOperation("Modification d'un enregistrement")
                    ->setNomTable($nomTable)
                    ->setIdTable($idTable)
                    ->setUser($this->security->getUser())
                ;

                $this->entityManager->persist($historique);
                $this->entityManager->flush();
            }
        }

        if (($method === Request::METHOD_PUT) || ($method === Request::METHOD_PATCH)) {
            $historique = (new Historique())
                ->setOperation("Modification d'un enregistrement")
                ->setNomTable($nomTable)
                ->setIdTable($idTable)
                ->setUser($this->security->getUser())
            ;

            $this->entityManager->persist($historique);
            $this->entityManager->flush();
        }

        if ($method === Request::METHOD_DELETE) {
            $entity = $request->attributes->get('data');

            $nomTable = explode('\\', $entity::class);
            $nomTable = $nomTable[(count($nomTable) - 1)];

            $idTable = $entity->getId();

            $historique = (new Historique())
                ->setOperation("Suppression d'un enregistrement")
                ->setNomTable($nomTable)
                ->setIdTable($idTable)
                ->setUser($this->security->getUser())
            ;

            $this->entityManager->persist($historique);
            $this->entityManager->flush();
        }
    }

}
