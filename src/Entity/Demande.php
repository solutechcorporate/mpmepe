<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
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
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:Demande','read:Entity']],
    denormalizationContext: ['groups' => ['write:Demande','write:Entity']],
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
class Demande
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:Demande',
        'read:ContactValeurDemande'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:Demande',
        'write:Demande',
        'read:ContactValeurDemande'
    ])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbLiaison = null;

    #[ORM\OneToMany(mappedBy: 'demande', targetEntity: ValeurDemande::class)]
    #[Groups([
        'read:Demande',
    ])]
    private Collection $valeurDemandes;

    public function __construct()
    {
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = "0";
        $this->valeurDemandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNbLiaison(): ?int
    {
        return $this->nbLiaison;
    }

    public function setNbLiaison(?int $nbLiaison): static
    {
        $this->nbLiaison = $nbLiaison;

        return $this;
    }

    /**
     * @return Collection<int, ValeurDemande>
     */
    public function getValeurDemandes(): Collection
    {
        return $this->valeurDemandes;
    }

    public function addValeurDemande(ValeurDemande $valeurDemande): static
    {
        if (!$this->valeurDemandes->contains($valeurDemande)) {
            $this->valeurDemandes->add($valeurDemande);
            $valeurDemande->setDemande($this);
        }

        return $this;
    }

    public function removeValeurDemande(ValeurDemande $valeurDemande): static
    {
        if ($this->valeurDemandes->removeElement($valeurDemande)) {
            // set the owning side to null (unless already changed)
            if ($valeurDemande->getDemande() === $this) {
                $valeurDemande->setDemande(null);
            }
        }

        return $this;
    }

}
