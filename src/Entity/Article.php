<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\AjouterArticleAction;
use App\InterfacePersonnalise\UserOwnedInterface;
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
use App\Service\ConvertValueToBoolService;
use App\Utils\Traits\EntityTimestampTrait;
use App\Utils\Traits\UserAjoutModifTrait;
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
        new Get(),
        new GetCollection(),
        new Post(
            controller: AjouterArticleAction::class,
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
#[UniqueEntity(
    fields: 'titre'
)]
#[ApiFilter(OrderFilter::class, properties: ['titre'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact', 'userAjout' => 'exact', 'userModif' => 'exact'])]
class Article implements UserOwnedInterface
{
    use EntityTimestampTrait;
    use UserAjoutModifTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:Article',
    ])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $titre = null;

    #[ORM\Column]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $contenu = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
    ])]
    private ?string $imageCodeFichier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?\DateTimeInterface $dateEvent = null;

    #[ORM\Column]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private string|bool|null $visibility = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\Column]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private string|bool|null $isMention = null;

    #[ORM\Column]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private string|bool|null $isFlashinfo = null;

    #[ORM\Column]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?int $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag4 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag5 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag6 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag7 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag8 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag9 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Article',
        'write:Article',
    ])]
    private ?string $tag10 = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbLiaison = null;

    #[Groups([
        'read:Article',
    ])]
    public array $fichiers = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
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

    public function getImageCodeFichier(): ?string
    {
        return $this->imageCodeFichier;
    }

    public function setImageCodeFichier(string $imageCodeFichier): static
    {
        $this->imageCodeFichier = $imageCodeFichier;

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

    public function setVisibility(string|bool|null $visibility): static
    {
        $this->visibility = ConvertValueToBoolService::convertValueToBool($visibility);

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

    public function setIsMention(string|bool|null $isMention): static
    {
        $this->isMention = ConvertValueToBoolService::convertValueToBool($isMention);

        return $this;
    }

    public function isIsFlashinfo(): ?bool
    {
        return $this->isFlashinfo;
    }

    public function setIsFlashinfo(string|bool|null $isFlashinfo): static
    {
        $this->isFlashinfo = ConvertValueToBoolService::convertValueToBool($isFlashinfo);

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

    public function getNbLiaison(): ?int
    {
        return $this->nbLiaison;
    }

    public function setNbLiaison(?int $nbLiaison): static
    {
        $this->nbLiaison = $nbLiaison;

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
