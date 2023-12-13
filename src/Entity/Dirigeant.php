<?php

namespace App\Entity;

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
        new Get(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            validationContext: ['groups' => ['Default']],
            inputFormats: ['multipart' => ['multipart/form-data']],
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
class Dirigeant
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomPrenoms = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $debutFonction = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $finFonction = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biographie = null;

    #[ORM\Column(length: 255)]
    private ?string $lienDecret = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\Column]
    private ?bool $isMinistre = null;

    #[ORM\OneToMany(mappedBy: 'ministreActuel', targetEntity: Ministere::class)]
    private Collection $ministeres;

    #[ORM\OneToMany(mappedBy: 'directeurActuel', targetEntity: Direction::class)]
    private Collection $directions;

    public function __construct()
    {
        $this->ministeres = new ArrayCollection();
        $this->directions = new ArrayCollection();
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
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

    /**
     * @return Collection<int, Ministere>
     */
    public function getMinisteres(): Collection
    {
        return $this->ministeres;
    }

    public function addMinistere(Ministere $ministere): static
    {
        if (!$this->ministeres->contains($ministere)) {
            $this->ministeres->add($ministere);
            $ministere->setMinistreActuel($this);
        }

        return $this;
    }

    public function removeMinistere(Ministere $ministere): static
    {
        if ($this->ministeres->removeElement($ministere)) {
            // set the owning side to null (unless already changed)
            if ($ministere->getMinistreActuel() === $this) {
                $ministere->setMinistreActuel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Direction>
     */
    public function getDirections(): Collection
    {
        return $this->directions;
    }

    public function addDirection(Direction $direction): static
    {
        if (!$this->directions->contains($direction)) {
            $this->directions->add($direction);
            $direction->setDirecteurActuel($this);
        }

        return $this;
    }

    public function removeDirection(Direction $direction): static
    {
        if ($this->directions->removeElement($direction)) {
            // set the owning side to null (unless already changed)
            if ($direction->getDirecteurActuel() === $this) {
                $direction->setDirecteurActuel(null);
            }
        }

        return $this;
    }
}
