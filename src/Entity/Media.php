<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media extends AbstractEntity
{
    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $websiteUrl = null;

    /**
     * @var Collection<int, PressMention>
     */
    #[ORM\OneToMany(mappedBy: 'media', targetEntity: PressMention::class)]
    private Collection $pressMentions;

    public function __construct()
    {
        parent::__construct();
        $this->pressMentions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(?string $websiteUrl): self
    {
        $this->websiteUrl = $websiteUrl;
        return $this;
    }

    /**
     * @return Collection<int, PressMention>
     */
    public function getPressMentions(): Collection
    {
        return $this->pressMentions;
    }

    public function addPressMention(PressMention $pressMention): self
    {
        if (!$this->pressMentions->contains($pressMention)) {
            $this->pressMentions->add($pressMention);
            $pressMention->setMedia($this);
        }
        return $this;
    }

    public function removePressMention(PressMention $pressMention): self
    {
        if ($this->pressMentions->removeElement($pressMention)) {
            // set the owning side to null (unless already changed)
            if ($pressMention->getMedia() === $this) {
                $pressMention->setMedia(null);
            }
        }
        return $this;
    }
}
