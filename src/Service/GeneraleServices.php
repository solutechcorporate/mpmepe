<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

//use NumberToWords\NumberToWords;

class GeneraleServices
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private EventDispatcherInterface        $eventDispatcher,
        private TokenStorageInterface           $tokenStorage
    )
    {
    }

    public function forceLogout(Request $request): void
    {
        $logoutEvent = new LogoutEvent($request, $this->tokenStorage->getToken());
        $this->eventDispatcher->dispatch($logoutEvent);
        $this->tokenStorage->setToken(null);
    }

    public function getTableNamePrefix($entity): string
    {
        return strtoupper(substr((string)$this->entityManager->getClassMetadata($entity)->getTableName(), 0, 3));
    }

    public function getTableName(string $entity): ?string
    {
        return $this->entityManager->getClassMetadata($entity)
            ->getTableName();
    }

    public function checkIfEntityHasField($entity, $field): bool
    {
        $entityModel = $this->entityManager->getClassMetadata($entity);
        return $entityModel->hasField($field);
    }

    public function checkIfEntityHasAssociation($entity, $field): bool
    {
        $entityModel = $this->entityManager->getClassMetadata($entity);
        return $entityModel->hasAssociation($field);
    }

    public function group_arra_by_key($key, $data)
    {
        $result = [];

        foreach ($data as $singleData) {
            if (array_key_exists($key, $singleData)) {
                $result[$singleData[$key]][] = $singleData;
            } else {
                $result[''][] = $singleData;
            }
        }
        return $result;
    }

    public function RemoveBS(string $Str): string
    {
        $StrArr = str_split($Str);
        $NewStr = '';
        foreach ($StrArr as $Char) {
            $CharNo = ord($Char);
            if ($CharNo === 163) {
                $NewStr .= $Char;
                continue;
            } // keep £
            if ($CharNo > 31 && $CharNo < 127) {
                $NewStr .= $Char;
            }
        }
        return $NewStr;
    }
}
