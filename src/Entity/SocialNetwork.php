<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\AjouterSocialNetworkAction;
use App\Controller\Delete\DeleteSocialNetworkAction;
use App\InterfacePersonnalise\UserOwnedInterface;
use App\Repository\SocialNetworkRepository;
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
use App\Service\ConvertValueToBoolService;
use App\Utils\Traits\EntityTimestampTrait;
use App\Utils\Traits\UserAjoutModifTrait;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocialNetworkRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:SocialNetwork','read:Entity']],
    denormalizationContext: ['groups' => ['write:SocialNetwork','write:Entity']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: AjouterSocialNetworkAction::class,
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
            security: "is_granted('ROLE_ADMIN')",
            controller: DeleteSocialNetworkAction::class,
            write: false
        )
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'nom', 'affichage'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact', 'userAjout' => 'exact', 'userModif' => 'exact'])]
class SocialNetwork implements UserOwnedInterface
{
    use EntityTimestampTrait;
    use UserAjoutModifTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:SocialNetwork',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:SocialNetwork',
    ])]
    private ?string $imageCodeFichier = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:SocialNetwork',
        'write:SocialNetwork',
    ])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups([
        'read:SocialNetwork',
        'write:SocialNetwork',
    ])]
    private ?string $lien = null;

    #[ORM\Column]
    #[Groups([
        'read:SocialNetwork',
        'write:SocialNetwork',
    ])]
    private ?int $affichage = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:SocialNetwork',
        'write:SocialNetwork',
    ])]
    private string|bool|null $headerIsSelect = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:SocialNetwork',
        'write:SocialNetwork',
    ])]
    private string|bool|null $footerIsSelect = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:SocialNetwork',
        'write:SocialNetwork',
    ])]
    private string|bool|null $contactIsSelect = null;

    #[Groups([
        'read:SocialNetwork',
    ])]
    public array $fichiers = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'read:SocialNetwork',
    ])]
    private ?User $userAjout = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'read:SocialNetwork',
    ])]
    private ?User $userModif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getImageCodeFichier(): ?string
    {
        return $this->imageCodeFichier;
    }

    public function setImageCodeFichier(string $imageCodeFichier): static
    {
        $this->imageCodeFichier = $imageCodeFichier;

        return $this;
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

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): static
    {
        $this->lien = $lien;

        return $this;
    }

    public function getAffichage(): ?int
    {
        return $this->affichage;
    }

    public function setAffichage(int $affichage): static
    {
        $this->affichage = $affichage;

        return $this;
    }

    public function isHeaderIsSelect(): ?bool
    {
        return $this->headerIsSelect;
    }

    public function setHeaderIsSelect(string|bool|null $headerIsSelect): static
    {
        $this->headerIsSelect = ConvertValueToBoolService::convertValueToBool($headerIsSelect);

        return $this;
    }

    public function isFooterIsSelect(): ?bool
    {
        return $this->footerIsSelect;
    }

    public function setFooterIsSelect(string|bool|null $footerIsSelect): static
    {
        $this->footerIsSelect = ConvertValueToBoolService::convertValueToBool($footerIsSelect);

        return $this;
    }

    public function isContactIsSelect(): ?bool
    {
        return $this->contactIsSelect;
    }

    public function setContactIsSelect(string|bool|null $contactIsSelect): static
    {
        $this->contactIsSelect = ConvertValueToBoolService::convertValueToBool($contactIsSelect);

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
