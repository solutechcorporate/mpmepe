<?php

namespace App\Utils\Traits;

use App\Service\ConvertValueToBoolService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait EntityTimestampTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['read:Entity'])]
    private ?\DateTimeImmutable $dateAjout = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['read:Entity', 'write:Entity'])]
    private ?\DateTimeInterface $dateModif = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:Entity', 'write:Entity'])]
    private string|bool|null $deleted = false;

    public function __construct()
    {
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = "0";
    }

    public function getDateAjout(): ?\DateTimeImmutable
    {
        return $this->dateAjout;
    }

    public function setDateAjout(?\DateTimeImmutable $dateAjout): self
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDateModif(?\DateTimeInterface $dateModif): self
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(string|bool|null $deleted): self
    {
        $this->deleted = ConvertValueToBoolService::convertValueToBool($deleted);

        return $this;
    }

}
