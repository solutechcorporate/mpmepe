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
use App\Controller\Delete\DeleteValeurDemandeAction;
use App\InterfacePersonnalise\UserOwnedInterface;
use App\Repository\ValeurDemandeRepository;
use App\Utils\Traits\EntityTimestampTrait;
use App\Utils\Traits\UserAjoutModifTrait;
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
            security: "is_granted('ROLE_ADMIN')",
            controller: DeleteValeurDemandeAction::class,
            write: false
        )
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'optionValue'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact', 'userAjout' => 'exact', 'userModif' => 'exact'])]
class ValeurDemande implements UserOwnedInterface
{
    use EntityTimestampTrait;
    use UserAjoutModifTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:ValeurDemande',
        'read:ContactValeurDemande',
        'read:Demande',
    ])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups([
        'read:ValeurDemande',
        'write:ValeurDemande',
        'read:ContactValeurDemande',
        'read:Demande',
    ])]
    private ?string $optionValue = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbLiaison = null;

    #[ORM\ManyToOne(inversedBy: 'valeurDemandes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:ValeurDemande',
        'write:ValeurDemande',
        'read:ContactValeurDemande'
    ])]
    private ?Demande $demande = null;

    #[ORM\OneToMany(mappedBy: 'valeurDemande', targetEntity: ContactValeurDemande::class)]
    private Collection $contactValeurDemandes;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'read:ValeurDemande',
    ])]
    private ?User $userAjout = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'read:ValeurDemande',
    ])]
    private ?User $userModif = null;

    public function __construct()
    {
        $this->contactValeurDemandes = new ArrayCollection();
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = "0";
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
