<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:Article','read:Entity']],
    denormalizationContext: ['groups' => ['write:Article','write:Entity']],
    operations: [
        new Get(
        
        ),
        new GetCollection(
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
#[UniqueEntity(
    fields: 'titre'
)]
class Article
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\Column(length: 255)]
    private ?string $imagePath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEvent = null;

    #[ORM\Column]
    private ?bool $visibility = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\Column]
    private ?bool $isMention = null;

    #[ORM\Column]
    private ?bool $isFlashinfo = null;

    #[ORM\Column]
    private ?int $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag4 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag5 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag6 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag7 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag8 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag9 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag10 = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
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

    public function getDateEvent(): ?\DateTimeInterface
    {
        return $this->dateEvent;
    }

    public function setDateEvent(\DateTimeInterface $dateEvent): static
    {
        $this->dateEvent = $dateEvent;

        return $this;
    }

    public function isVisibility(): ?bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): static
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function isIsMention(): ?bool
    {
        return $this->isMention;
    }

    public function setIsMention(bool $isMention): static
    {
        $this->isMention = $isMention;

        return $this;
    }

    public function isIsFlashinfo(): ?bool
    {
        return $this->isFlashinfo;
    }

    public function setIsFlashinfo(bool $isFlashinfo): static
    {
        $this->isFlashinfo = $isFlashinfo;

        return $this;
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(int $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTag1(): ?string
    {
        return $this->tag1;
    }

    public function setTag1(string $tag1): static
    {
        $this->tag1 = $tag1;

        return $this;
    }

    public function getTag2(): ?string
    {
        return $this->tag2;
    }

    public function setTag2(string $tag2): static
    {
        $this->tag2 = $tag2;

        return $this;
    }

    public function getTag3(): ?string
    {
        return $this->tag3;
    }

    public function setTag3(?string $tag3): static
    {
        $this->tag3 = $tag3;

        return $this;
    }

    public function getTag4(): ?string
    {
        return $this->tag4;
    }

    public function setTag4(?string $tag4): static
    {
        $this->tag4 = $tag4;

        return $this;
    }

    public function getTag5(): ?string
    {
        return $this->tag5;
    }

    public function setTag5(?string $tag5): static
    {
        $this->tag5 = $tag5;

        return $this;
    }

    public function getTag6(): ?string
    {
        return $this->tag6;
    }

    public function setTag6(?string $tag6): static
    {
        $this->tag6 = $tag6;

        return $this;
    }

    public function getTag7(): ?string
    {
        return $this->tag7;
    }

    public function setTag7(?string $tag7): static
    {
        $this->tag7 = $tag7;

        return $this;
    }

    public function getTag8(): ?string
    {
        return $this->tag8;
    }

    public function setTag8(?string $tag8): static
    {
        $this->tag8 = $tag8;

        return $this;
    }

    public function getTag9(): ?string
    {
        return $this->tag9;
    }

    public function setTag9(?string $tag9): static
    {
        $this->tag9 = $tag9;

        return $this;
    }

    public function getTag10(): ?string
    {
        return $this->tag10;
    }

    public function setTag10(?string $tag10): static
    {
        $this->tag10 = $tag10;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
