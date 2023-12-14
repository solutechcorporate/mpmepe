<?php

namespace App\Entity;

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
use App\Repository\DocumentCategorieDocumentRepository;
use App\Utils\Traits\EntityTimestampTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: DocumentCategorieDocumentRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:DocumentCategorieDocument','read:Entity']],
    denormalizationContext: ['groups' => ['write:DocumentCategorieDocument','write:Entity']],
    operations: [
        new Get(),
        new GetCollection(),
//        new Post(
//            validationContext: ['groups' => ['Default']],
//            security: "is_granted('ROLE_ADMIN')"
//        ),
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
class DocumentCategorieDocument
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:DocumentCategorieDocument',
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documentCategorieDocuments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:DocumentCategorieDocument',
        'write:DocumentCategorieDocument',
    ])]
    private ?Document $document = null;

    #[ORM\ManyToOne(inversedBy: 'documentCategorieDocuments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'read:DocumentCategorieDocument',
        'write:DocumentCategorieDocument',
    ])]
    private ?CategorieDocument $categorieDocument = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function getCategorieDocument(): ?CategorieDocument
    {
        return $this->categorieDocument;
    }

    public function setCategorieDocument(?CategorieDocument $categorieDocument): static
    {
        $this->categorieDocument = $categorieDocument;

        return $this;
    }
}
