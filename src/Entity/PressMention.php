<?php

namespace App\Entity;

use App\Repository\PressMentionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PressMentionRepository::class)]
class PressMention extends AbstractEntity
{
    public const TYPE_ARTICLE = 'Article';
    public const TYPE_TRIBUNE = 'Tribune';
    public const TYPE_DEPECHE = 'Dépêche';
    public const TYPE_INTERVIEW = 'Interview';
    public const TYPE_ANNONCE = 'Annonce';

    public const TYPES = [
        self::TYPE_ARTICLE => self::TYPE_ARTICLE,
        self::TYPE_TRIBUNE => self::TYPE_TRIBUNE,
        self::TYPE_DEPECHE => self::TYPE_DEPECHE,
        self::TYPE_INTERVIEW => self::TYPE_INTERVIEW,
        self::TYPE_ANNONCE => self::TYPE_ANNONCE,
    ];

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 50, options: ['default' => self::TYPE_ARTICLE])]
    private string $type = self::TYPE_ARTICLE;

    #[ORM\Column(length: 500)]
    private ?string $externalLink = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $publishedAt = null;

    #[ORM\ManyToOne(inversedBy: 'pressMentions')]
    private ?Media $media = null;

    #[ORM\ManyToOne(inversedBy: 'pressMentions')]
    private ?SpecialPage $specialPage = null;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getExternalLink(): ?string
    {
        return $this->externalLink;
    }

    public function setExternalLink(?string $externalLink): self
    {
        $this->externalLink = $externalLink;
        return $this;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;
        return $this;
    }

    public function getSpecialPage(): ?SpecialPage
    {
        return $this->specialPage;
    }

    public function setSpecialPage(?SpecialPage $specialPage): self
    {
        $this->specialPage = $specialPage;
        return $this;
    }
}
