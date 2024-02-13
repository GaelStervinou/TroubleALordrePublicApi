<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\Action\CreateMediaAction;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Vich\Uploadable()]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
        ),
        new Post(
            controller: CreateMediaAction::class,
            openapi: new Operation(
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ])
                )
            ),
            deserialize: false,
        ),
        new Patch(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['media:read']],
    denormalizationContext: ['groups' => ['media:write']],
)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['company:collection:read', 'company:read', 'media:read', 'user:companies:read'])]
    private ?UuidInterface $id = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['media:read', 'company:collection:read', 'company:read', 'company:dashboard:read', 'rate:by-user:read', 'user:read', 'user:companies:read', 'user:reservation:read'])]
    public ?string $contentUrl = null;

    #[UploadableField(mapping: 'media', fileNameProperty: 'path')]
    #[NotNull]
    public ?File $file = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:collection:read', 'company:read'])]
    public ?string $path = null;

    #[ORM\ManyToOne(inversedBy: 'medias')]
    private ?Company $company = null;



    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): self
    {
        $this->contentUrl = $contentUrl;

        return $this;
    }
}
