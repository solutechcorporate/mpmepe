<?php

namespace App\Entity;

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
use App\Utils\Traits\EntityTimestampTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocialNetworkRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:SocialNetwork','read:Entity']],
    denormalizationContext: ['groups' => ['write:SocialNetwork','write:Entity']],
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            validationContext: ['groups' => ['Default']],
            inputFormats: ['multipart' => ['multipart/form-data']],
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
class SocialNetwork
{
    use EntityTimestampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $imagePath = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $lien = null;

    #[ORM\Column]
    private ?int $affichage = null;

    #[ORM\Column]
    private ?bool $headerIsSelect = null;

    #[ORM\Column]
    private ?bool $footerIsSelect = null;

    #[ORM\Column]
    private ?bool $contactIsSelect = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): static
    {
        $this->imagePath = $imagePath;

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

    public function setHeaderIsSelect(bool $headerIsSelect): static
    {
        $this->headerIsSelect = $headerIsSelect;

        return $this;
    }

    public function isFooterIsSelect(): ?bool
    {
        return $this->footerIsSelect;
    }

    public function setFooterIsSelect(bool $footerIsSelect): static
    {
        $this->footerIsSelect = $footerIsSelect;

        return $this;
    }

    public function isContactIsSelect(): ?bool
    {
        return $this->contactIsSelect;
    }

    public function setContactIsSelect(bool $contactIsSelect): static
    {
        $this->contactIsSelect = $contactIsSelect;

        return $this;
    }
}
