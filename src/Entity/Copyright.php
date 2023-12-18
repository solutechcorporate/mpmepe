<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\CopyrightRepository;
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
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CopyrightRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:Copyright','read:Entity']],
    denormalizationContext: ['groups' => ['write:Copyright','write:Entity']],
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
#[ApiFilter(OrderFilter::class, properties: ['texte', 'mentionLegale'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact', 'userAjout' => 'exact', 'userModif' => 'exact'])]
class Copyright
{
    use EntityTimestampTrait;
    use UserAjoutModifTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'read:Copyright',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:Copyright',
        'write:Copyright',
    ])]
    private ?string $texte = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'read:Copyright',
        'write:Copyright',
    ])]
    private ?string $mentionLegale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): static
    {
        $this->texte = $texte;

        return $this;
    }

    public function getMentionLegale(): ?string
    {
        return $this->mentionLegale;
    }

    public function setMentionLegale(string $mentionLegale): static
    {
        $this->mentionLegale = $mentionLegale;

        return $this;
    }
}
