<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\AjouterMinistereAction;
use App\InterfacePersonnalise\UserOwnedInterface;
use App\Repository\MinistereRepository;
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

#[ORM\Entity(repositoryClass: MinistereRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:Ministere','read:Entity']],
    denormalizationContext: ['groups' => ['write:Ministere','write:Entity']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: AjouterMinistereAction::class,
            write: false,
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
#[ApiFilter(OrderFilter::class, properties: ['id'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact', 'userAjout' => 'exact', 'userModif' => 'exact'])]
class Ministere implements UserOwnedInterface
{
    use EntityTimestampTrait;
    use UserAjoutModifTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:Ministere',
        'read:Direction',
        'read:Dirigeant',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Ministere',
        'read:Direction',
        'read:Dirigeant',
    ])]
    private ?string $logoCodeFichier = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups([
        'read:Ministere',
        'write:Ministere',
        'read:Direction',
        'read:Dirigeant',
    ])]
    private ?string $nomSite = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'read:Ministere',
        'write:Ministere',
        'read:Direction',
        'read:Dirigeant',
    ])]
    private ?string $adresse = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Ministere',
        'write:Ministere',
        'read:Direction',
        'read:Dirigeant',
    ])]
    private ?float $longitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Ministere',
        'write:Ministere',
        'read:Direction',
        'read:Dirigeant',
    ])]
    private ?float $latitude = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:Ministere',
        'write:Ministere',
        'read:Direction',
        'read:Dirigeant',
    ])]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:Ministere',
        'write:Ministere',
        'read:Direction',
        'read:Dirigeant',
    ])]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbLiaison = null;

    #[ORM\OneToMany(mappedBy: 'ministere', targetEntity: Dirigeant::class)]
    #[Groups([
        'read:Ministere',
    ])]
    private Collection $dirigeants;

    #[Groups([
        'read:Ministere',
        'read:Direction',
        'read:Dirigeant',
    ])]
    public array $fichiers = [];

    public function __construct()
    {
        $this->dirigeants = new ArrayCollection();
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = "0";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getLogoCodeFichier(): ?string
    {
        return $this->logoCodeFichier;
    }

    public function setLogoCodeFichier(string $logoCodeFichier): static
    {
        $this->logoCodeFichier = $logoCodeFichier;

        return $this;
    }

    public function getNomSite(): ?string
    {
        return $this->nomSite;
    }

    public function setNomSite(string $nomSite): static
    {
        $this->nomSite = $nomSite;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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
     * @return Collection<int, Dirigeant>
     */
    public function getDirigeants(): Collection
    {
        return $this->dirigeants;
    }

    public function addDirigeant(Dirigeant $dirigeant): static
    {
        if (!$this->dirigeants->contains($dirigeant)) {
            $this->dirigeants->add($dirigeant);
            $dirigeant->setMinistere($this);
        }

        return $this;
    }

    public function removeDirigeant(Dirigeant $dirigeant): static
    {
        if ($this->dirigeants->removeElement($dirigeant)) {
            // set the owning side to null (unless already changed)
            if ($dirigeant->getMinistere() === $this) {
                $dirigeant->setMinistere(null);
            }
        }

        return $this;
    }

    public function getFichiers(): array
    {
        return $this->fichiers;
    }

    public function setFichiers(array $fichiers)
    {
        $this->fichiers = $fichiers;
        return $this;
    }

}
