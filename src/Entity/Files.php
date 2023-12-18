<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\FilesRepository;
use App\Service\ConvertValueToBoolService;
use App\Utils\Traits\EntityTimestampTrait;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: FilesRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:Files','read:Entity']],
    denormalizationContext: ['groups' => ['write:Files','write:Entity']],
    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
//        new Post(
//            validationContext: ['groups' => ['Default']],
//            inputFormats: ['multipart' => ['multipart/form-data']],
//            security: "is_granted('ROLE_ADMIN')"
//        ),
//        new Put(
//            security: "is_granted('ROLE_ADMIN')"
//        ),
//        new Patch(
//            security: "is_granted('ROLE_ADMIN')"
//        ),
//        new Delete(
//            security: "is_granted('ROLE_ADMIN')"
//        )
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['filename', 'type'])]
#[ApiFilter(SearchFilter::class, properties: ['deleted' => 'exact'])]
class Files
{
    use EntityTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[Groups([
        'read:Files',
    ])]
    private ?Ulid $id = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    #[Groups([
        'read:Files',
        'write:Files',
    ])]
    private string $filename;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    #[Groups([
        'read:Files',
    ])]
    private string $type;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    #[Groups([
        'read:Files',
    ])]
    private string $location;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::FLOAT)]
    #[Groups([
        'read:Files',
    ])]
    private float $size;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    #[Groups([
        'read:Files',
    ])]
    private string $referenceCode;

    #[ORM\Column]
    private string|bool|null $temp = false;

    public function __construct()
    {
        $this->size = 0;
        $this->dateAjout = new \DateTimeImmutable();
        $this->dateModif = new \DateTime();
        $this->deleted = "0";
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getReferenceCode(): ?string
    {
        return $this->referenceCode;
    }

    public function setReferenceCode(string $referenceCode): self
    {
        $this->referenceCode = $referenceCode;

        return $this;
    }

    public function isTemp(): ?bool
    {
        return $this->temp;
    }

    public function setTemp(string|bool|null $temp): self
    {
        $this->temp = ConvertValueToBoolService::convertValueToBool($temp);

        return $this;
    }

}
