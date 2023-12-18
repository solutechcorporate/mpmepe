<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\CategorieDocumentRepository;
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
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieDocumentRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:CategorieDocument','read:Entity']],
    denormalizationContext: ['groups' => ['write:CategorieDocument','write:Entity']],
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
#[ApiFilter(OrderFilter::class, properties: ['nom'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact', 'userAjout' => 'exact', 'userModif' => 'exact'])]
class CategorieDocument
{
    use EntityTimestampTrait;
    use UserAjoutModifTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:CategorieDocument',
        'read:DocumentCategorieDocument',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:CategorieDocument',
        'write:CategorieDocument',
        'read:DocumentCategorieDocument',
    ])]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbLiaison = null;

    #[ORM\OneToMany(mappedBy: 'categorieDocument', targetEntity: DocumentCategorieDocument::class)]
    private Collection $documentCategorieDocuments;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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
            $documentCategorieDocument->setCategorieDocument($this);
        }

        return $this;
    }

    public function removeDocumentCategorieDocument(DocumentCategorieDocument $documentCategorieDocument): static
    {
        if ($this->documentCategorieDocuments->removeElement($documentCategorieDocument)) {
            // set the owning side to null (unless already changed)
            if ($documentCategorieDocument->getCategorieDocument() === $this) {
                $documentCategorieDocument->setCategorieDocument(null);
            }
        }

        return $this;
    }

}
