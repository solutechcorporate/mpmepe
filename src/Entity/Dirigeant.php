<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\DirigeantRepository;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Utils\Traits\EntityTimestampTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DirigeantRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:Dirigeant','read:Entity']],
    denormalizationContext: ['groups' => ['write:Dirigeant','write:Entity']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            validationContext: ['groups' => ['Default']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'nomPrenoms', 'debutFonction'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact', 'userAjout' => 'exact', 'userModif'])]
class Dirigeant
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?string $nomPrenoms = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?\DateTimeInterface $debutFonction = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?\DateTimeInterface $finFonction = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?string $biographie = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?string $lienDecret = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?string $intitule = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?bool $isMinistre = null;

    #[ORM\ManyToOne(inversedBy: 'dirigeants')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
    ])]
    private ?Ministere $ministere = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?bool $isMinistreActuel = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?bool $isDirecteur = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Direction',
        'read:Ministere',
    ])]
    private ?bool $isDirecteurActuel = null;

    #[ORM\ManyToOne(inversedBy: 'dirigeants')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:Dirigeant',
        'write:Dirigeant',
        'read:Ministere',
    ])]
    private ?Direction $direction = null;

    public function __construct()
    {
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = "0";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPrenoms(): ?string
    {
        return $this->nomPrenoms;
    }

    public function setNomPrenoms(string $nomPrenoms): static
    {
        $this->nomPrenoms = $nomPrenoms;

        return $this;
    }

    public function getDebutFonction(): ?\DateTimeInterface
    {
        return $this->debutFonction;
    }

    public function setDebutFonction(\DateTimeInterface $debutFonction): static
    {
        $this->debutFonction = $debutFonction;

        return $this;
    }

    public function getFinFonction(): ?\DateTimeInterface
    {
        return $this->finFonction;
    }

    public function setFinFonction(\DateTimeInterface $finFonction): static
    {
        $this->finFonction = $finFonction;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getLienDecret(): ?string
    {
        return $this->lienDecret;
    }

    public function setLienDecret(string $lienDecret): static
    {
        $this->lienDecret = $lienDecret;

        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): static
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function isIsMinistre(): ?bool
    {
        return $this->isMinistre;
    }

    public function setIsMinistre(bool $isMinistre): static
    {
        $this->isMinistre = $isMinistre;

        return $this;
    }

    public function getMinistere(): ?Ministere
    {
        return $this->ministere;
    }

    public function setMinistere(?Ministere $ministere): static
    {
        $this->ministere = $ministere;

        return $this;
    }

    public function isIsMinistreActuel(): ?bool
    {
        return $this->isMinistreActuel;
    }

    public function setIsMinistreActuel(?bool $isMinistreActuel): static
    {
        $this->isMinistreActuel = $isMinistreActuel;

        return $this;
    }

    public function isIsDirecteur(): ?bool
    {
        return $this->isDirecteur;
    }

    public function setIsDirecteur(?bool $isDirecteur): static
    {
        $this->isDirecteur = $isDirecteur;

        return $this;
    }

    public function isIsDirecteurActuel(): ?bool
    {
        return $this->isDirecteurActuel;
    }

    public function setIsDirecteurActuel(?bool $isDirecteurActuel): static
    {
        $this->isDirecteurActuel = $isDirecteurActuel;

        return $this;
    }

    public function getDirection(): ?Direction
    {
        return $this->direction;
    }

    public function setDirection(?Direction $direction): static
    {
        $this->direction = $direction;

        return $this;
    }
}
