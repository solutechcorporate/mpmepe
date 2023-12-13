<?php

namespace App\Entity;

use App\Repository\PageHeaderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageHeaderRepository::class)]
class PageHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pageHeaders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Page $page = null;

    #[ORM\ManyToOne(inversedBy: 'pageHeaders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Header $header = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): static
    {
        $this->page = $page;

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
}
