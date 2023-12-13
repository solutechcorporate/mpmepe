<?php

namespace App\Entity;

use App\Repository\DocumentCategorieDocumentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentCategorieDocumentRepository::class)]
class DocumentCategorieDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documentCategorieDocuments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Document $document = null;

    #[ORM\ManyToOne(inversedBy: 'documentCategorieDocuments')]
    #[ORM\JoinColumn(nullable: false)]
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
