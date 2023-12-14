<?php

namespace App\Entity;

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
use App\Repository\ValeurDemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValeurDemandeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:ValeurDemande','read:Entity']],
    denormalizationContext: ['groups' => ['write:ValeurDemande','write:Entity']],
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
class ValeurDemande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $optionValue = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbLiaison = null;

    #[ORM\ManyToOne(inversedBy: 'valeurDemandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Demande $demande = null;

    #[ORM\OneToMany(mappedBy: 'valeurDemande', targetEntity: ContactValeurDemande::class)]
    private Collection $contactValeurDemandes;

    public function __construct()
    {
        $this->contactValeurDemandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOptionValue(): ?string
    {
        return $this->optionValue;
    }

    public function setOptionValue(string $optionValue): static
    {
        $this->optionValue = $optionValue;

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

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): static
    {
        $this->demande = $demande;

        return $this;
    }

    /**
     * @return Collection<int, ContactValeurDemande>
     */
    public function getContactValeurDemandes(): Collection
    {
        return $this->contactValeurDemandes;
    }

    public function addContactValeurDemande(ContactValeurDemande $contactValeurDemande): static
    {
        if (!$this->contactValeurDemandes->contains($contactValeurDemande)) {
            $this->contactValeurDemandes->add($contactValeurDemande);
            $contactValeurDemande->setValeurDemande($this);
        }

        return $this;
    }

    public function removeContactValeurDemande(ContactValeurDemande $contactValeurDemande): static
    {
        if ($this->contactValeurDemandes->removeElement($contactValeurDemande)) {
            // set the owning side to null (unless already changed)
            if ($contactValeurDemande->getValeurDemande() === $this) {
                $contactValeurDemande->setValeurDemande(null);
            }
        }

        return $this;
    }
}
