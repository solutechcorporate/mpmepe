<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\AjouterDocumentAction;
use App\Repository\DocumentRepository;
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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:Document','read:Entity']],
    denormalizationContext: ['groups' => ['write:Document','write:Entity']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: AjouterDocumentAction::class,
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
#[ApiFilter(OrderFilter::class, properties: ['id', 'titre'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact', 'userAjout' => 'exact', 'userModif'])]
class Document
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:Document',
        'read:DocumentCategorieDocument',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'read:Document',
        'read:DocumentCategorieDocument',
    ])]
    private ?string $docCodeFichier = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups([
        'read:Document',
        'write:Document',
        'read:DocumentCategorieDocument',
    ])]
    private ?string $titre = null;

    #[ORM\Column]
    #[Groups([
        'read:Document',
        'write:Document',
        'read:DocumentCategorieDocument',
    ])]
    private ?bool $visibility = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([
        'read:Document',
        'write:Document',
        'read:DocumentCategorieDocument',
    ])]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbLiaison = null;

    #[ORM\OneToMany(mappedBy: 'document', targetEntity: DocumentCategorieDocument::class)]
    private Collection $documentCategorieDocuments;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Document',
        'write:Document',
        'read:DocumentCategorieDocument',
    ])]
    private ?float $nbLecture = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Document',
        'write:Document',
        'read:DocumentCategorieDocument',
    ])]
    private ?float $nbTelechargement = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Document',
        'read:DocumentCategorieDocument',
    ])]
    private ?float $tailleFichier = null;

    #[Groups([
        'read:Document',
        'read:DocumentCategorieDocument',
    ])]
    public array $fichiers = [];

    #[ORM\Column(nullable: true)]
    #[Groups([
        'read:Document',
        'read:DocumentCategorieDocument',
    ])]
    private ?float $extensionFichier = null;

    public function __construct()
    {
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = "0";
        $this->documentCategorieDocuments = new ArrayCollection();
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

    public function getDocCodeFichier(): ?string
    {
        return $this->docCodeFichier;
    }

    public function setDocCodeFichier(string $docCodeFichier): static
    {
        $this->docCodeFichier = $docCodeFichier;

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
     * @return Collection<int, DocumentCategorieDocument>
     */
    public function getDocumentCategorieDocuments(): Collection
    {
        return $this->documentCategorieDocuments;
    }

    public function addDocumentCategorieDocument(DocumentCategorieDocument $documentCategorieDocument): static
    {
        if (!$this->documentCategorieDocuments->contains($documentCategorieDocument)) {
            $this->documentCategorieDocuments->add($documentCategorieDocument);
            $documentCategorieDocument->setDocument($this);
        }

        return $this;
    }

    public function removeDocumentCategorieDocument(DocumentCategorieDocument $documentCategorieDocument): static
    {
        if ($this->documentCategorieDocuments->removeElement($documentCategorieDocument)) {
            // set the owning side to null (unless already changed)
            if ($documentCategorieDocument->getDocument() === $this) {
                $documentCategorieDocument->setDocument(null);
            }
        }

        return $this;
    }

    public function getNbLecture(): ?float
    {
        return $this->nbLecture;
    }

    public function setNbLecture(?float $nbLecture): static
    {
        $this->nbLecture = $nbLecture;

        return $this;
    }

    public function getNbTelechargement(): ?float
    {
        return $this->nbTelechargement;
    }

    public function setNbTelechargement(?float $nbTelechargement): static
    {
        $this->nbTelechargement = $nbTelechargement;

        return $this;
    }

    public function getTailleFichier(): ?float
    {
        return $this->tailleFichier;
    }

    public function setTailleFichier(?float $tailleFichier): static
    {
        $this->tailleFichier = $tailleFichier;

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

    public function getExtensionFichier(): ?float
    {
        return $this->extensionFichier;
    }

    public function setExtensionFichier(?float $extensionFichier): static
    {
        $this->extensionFichier = $extensionFichier;

        return $this;
    }
}
