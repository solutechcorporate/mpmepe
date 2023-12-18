<?php

namespace App\Utils\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait UserAjoutModifTrait
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['read:Entity'])]
    private ?User $userAjout = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['read:Entity'])]
    private ?User $userModif = null;

    public function getUserAjout(): ?User
    {
        return $this->userAjout;
    }

    public function setUserAjout(?User $userAjout): static
    {
        $this->userAjout = $userAjout;

        return $this;
    }

    public function getUserModif(): ?User
    {
        return $this->userModif;
    }

    public function setUserModif(?User $userModif): static
    {
        $this->userModif = $userModif;

        return $this;
    }

}
