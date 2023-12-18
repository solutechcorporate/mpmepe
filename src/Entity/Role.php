<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\InterfacePersonnalise\UserOwnedInterface;
use App\Repository\RoleRepository;
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
use App\Utils\Traits\UserAjoutModifTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:Role','read:Entity']],
    denormalizationContext: ['groups' => ['write:Role','write:Entity']],
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
#[ApiFilter(OrderFilter::class, properties: ['id', 'nom', 'description'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact', 'userAjout' => 'exact', 'userModif' => 'exact'])]
class Role implements UserOwnedInterface
{
    use EntityTimestampTrait;
    use UserAjoutModifTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:Role',
        'read:UserRole',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:Role',
        'write:Role',
        'read:UserRole',
    ])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'read:Role',
        'write:Role',
        'read:UserRole',
    ])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbLiaison = null;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: UserRole::class)]
    private Collection $userRoles;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = "0";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
     * @return Collection<int, UserRole>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(UserRole $userRole): static
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles->add($userRole);
            $userRole->setRole($this);
        }

        return $this;
    }

    public function removeUserRole(UserRole $userRole): static
    {
        if ($this->userRoles->removeElement($userRole)) {
            // set the owning side to null (unless already changed)
            if ($userRole->getRole() === $this) {
                $userRole->setRole(null);
            }
        }

        return $this;
    }

}
