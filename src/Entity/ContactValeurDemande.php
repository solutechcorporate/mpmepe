<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ContactValeurDemandeRepository;
use App\Utils\Traits\EntityTimestampTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ContactValeurDemandeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:ContactValeurDemande','read:Entity']],
    denormalizationContext: ['groups' => ['write:ContactValeurDemande','write:Entity']],
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
//        new Post(
//            validationContext: ['groups' => ['Default']],
////            security: "is_granted('ROLE_ADMIN')"
//        ),
//        new Put(
//            security: "is_granted('ROLE_ADMIN')"
//        ),
//        new Patch(
//            security: "is_granted('ROLE_ADMIN')"
//        ),
//        new Delete(
//            security: "is_granted('ROLE_ADMIN')"
//        )
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['id'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact'])]
class ContactValeurDemande
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:ContactValeurDemande',
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contactValeurDemandes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:ContactValeurDemande',
        'write:ContactValeurDemande',
    ])]
    private ?Contact $contact = null;

    #[ORM\ManyToOne(inversedBy: 'contactValeurDemandes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:ContactValeurDemande',
        'write:ContactValeurDemande',
    ])]
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
