<?php

namespace App\Entity;

use App\Repository\ContactValeurDemandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactValeurDemandeRepository::class)]
class ContactValeurDemande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contactValeurDemandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contact $contact = null;

    #[ORM\ManyToOne(inversedBy: 'contactValeurDemandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ValeurDemande $valeurDemande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getValeurDemande(): ?ValeurDemande
    {
        return $this->valeurDemande;
    }

    public function setValeurDemande(?ValeurDemande $valeurDemande): static
    {
        $this->valeurDemande = $valeurDemande;

        return $this;
    }
}
