<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionException;

class ControlDeletionEntityService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @throws ReflectionException
     */
    public function controlDeletion(object $entity): void
    {
        $reflectionClass = new \ReflectionClass($entity::class);
        $supprimable = $reflectionClass->hasProperty('nbLiaison');

        if ($supprimable === true) {
            if (((int) $entity->getNbLiaison()) === 0) {
                $this->entityManager->remove($entity);
                $this->entityManager->flush();
            } else {
                $this->entityManager->refresh($entity);
                $entity->setDeleted(true);
                $this->entityManager->flush();
            }
        } else {
            // Entity ne contient pas la propriété nbLiaison
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }
    }

}
