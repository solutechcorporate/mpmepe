<?php

namespace App\InterfacePersonnalise;

use App\Entity\User;

interface UserOwnedInterface
{
    public function getUserModif(): ?User;

    public function setUserModif(?User $userModif): static;
}
