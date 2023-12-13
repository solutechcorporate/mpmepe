<?php

namespace App\Entity;

use App\Repository\HistoriqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueRepository::class)]
class Historique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $operation = null;

    #[ORM\Column(length: 255)]
    private ?string $nomTable = null;

    #[ORM\Column]
    private ?int $idTable = null;

    #[ORM\ManyToOne(inversedBy: 'historiques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): static
    {
        $this->operation = $operation;

        return $this;
    }

    public function getNomTable(): ?string
    {
        return $this->nomTable;
    }

    public function setNomTable(string $nomTable): static
    {
        $this->nomTable = $nomTable;

        return $this;
    }

    public function getIdTable(): ?int
    {
        return $this->idTable;
    }

    public function setIdTable(int $idTable): static
    {
        $this->idTable = $idTable;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
