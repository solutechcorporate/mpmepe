<?php

namespace App\Entity;

use App\Repository\MenuRepository;
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
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:Menu','read:Entity']],
    denormalizationContext: ['groups' => ['write:Menu','write:Entity']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            validationContext: ['groups' => ['Default']],
            inputFormats: ['multipart' => ['multipart/form-data']],
            security: "is_granted('ROLE_ADMIN')"
        ),
//        new Put(
//            security: "is_granted('ROLE_ADMIN')"
//        ),
//        new Patch(
//            security: "is_granted('ROLE_ADMIN')"
//        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]
class Menu
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:Menu',
        'write:Menu',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:Menu',
        'write:Menu',
    ])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:Menu',
        'write:Menu',
    ])]
    private ?Header $header = null;

    #[ORM\OneToMany(mappedBy: 'menu', targetEntity: SousMenu::class)]
    #[Groups([
        'read:Menu',
        'write:Menu',
    ])]
    private Collection $sousMenus;

    #[ORM\Column(nullable: true)]
    private ?int $nbLiaison = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'read:Menu',
        'write:Menu',
    ])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Menu',
        'write:Menu',
    ])]
    private ?string $imageCodeFichier = null;

    public function __construct()
    {
        $this->sousMenus = new ArrayCollection();
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = "0";
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

    public function getHeader(): ?Header
    {
        return $this->header;
    }

    public function setHeader(?Header $header): static
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return Collection<int, SousMenu>
     */
    public function getSousMenus(): Collection
    {
        return $this->sousMenus;
    }

    public function addSousMenu(SousMenu $sousMenu): static
    {
        if (!$this->sousMenus->contains($sousMenu)) {
            $this->sousMenus->add($sousMenu);
            $sousMenu->setMenu($this);
        }

        return $this;
    }

    public function removeSousMenu(SousMenu $sousMenu): static
    {
        if ($this->sousMenus->removeElement($sousMenu)) {
            // set the owning side to null (unless already changed)
            if ($sousMenu->getMenu() === $this) {
                $sousMenu->setMenu(null);
            }
        }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImageCodeFichier(): ?string
    {
        return $this->imageCodeFichier;
    }

    public function setImageCodeFichier(?string $imageCodeFichier): static
    {
        $this->imageCodeFichier = $imageCodeFichier;

        return $this;
    }
}
