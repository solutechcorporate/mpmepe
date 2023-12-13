<?php

namespace App\Entity;

use App\Repository\ValeurDemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValeurDemandeRepository::class)]
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
