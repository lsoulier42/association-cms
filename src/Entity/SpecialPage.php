<?php

namespace App\Entity;

use App\Repository\SpecialPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialPageRepository::class)]
class SpecialPage extends AbstractEntity
{
    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(length: 50, unique: true)]
    private string $identifier;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $showInMenu = false;

    #[ORM\Column(options: ['default' => 0])]
    private int $menuOrder = 0;

    #[ORM\ManyToOne(inversedBy: 'specialPages')]
    private ?Category $category = null;

    /**
     * @var Collection<int, PressMention>
     */
    #[ORM\OneToMany(mappedBy: 'specialPage', targetEntity: PressMention::class)]
    private Collection $pressMentions;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(mappedBy: 'specialPage', targetEntity: Appointment::class)]
    private Collection $appointments;

    public function __construct()
    {
        parent::__construct();
        $this->pressMentions = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function isShowInMenu(): bool
    {
        return $this->showInMenu;
    }

    public function setShowInMenu(bool $showInMenu): self
    {
        $this->showInMenu = $showInMenu;
        return $this;
    }

    public function getMenuOrder(): int
    {
        return $this->menuOrder;
    }

    public function setMenuOrder(int $menuOrder): self
    {
        $this->menuOrder = $menuOrder;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
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
            $pressMention->setSpecialPage($this);
        }
        return $this;
    }

    public function removePressMention(PressMention $pressMention): self
    {
        if ($this->pressMentions->removeElement($pressMention)) {
            if ($pressMention->getSpecialPage() === $this) {
                $pressMention->setSpecialPage(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setSpecialPage($this);
        }
        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            if ($appointment->getSpecialPage() === $this) {
                $appointment->setSpecialPage(null);
            }
        }
        return $this;
    }
}
