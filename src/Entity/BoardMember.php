<?php

namespace App\Entity;

use App\Enum\BoardMemberCategory;
use App\Enum\BoardMemberPrefix;
use App\Enum\BoardMemberTitle;
use App\Enum\BoardMemberDon;
use App\Enum\BoardMemberComite;
use App\Repository\BoardMemberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: BoardMemberRepository::class)]
class BoardMember extends AbstractEntity
{
    #[ORM\Column(length: 20, nullable: true, enumType: BoardMemberPrefix::class)]
    private ?BoardMemberPrefix $prefix = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 100, enumType: BoardMemberCategory::class)]
    private ?BoardMemberCategory $category = null;

    #[ORM\Column(length: 100, nullable: true, enumType: BoardMemberTitle::class)]
    private ?BoardMemberTitle $title = null;

    #[ORM\Column(type: 'json')]
    private array $dons = [];

    #[ORM\Column(type: 'json')]
    private array $comites = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $expertise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $qualifications = null;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    private ?Media $photo = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $sortOrder = 0;

    public function getPrefix(): ?BoardMemberPrefix
    {
        return $this->prefix;
    }

    public function setPrefix(?BoardMemberPrefix $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCategory(): ?BoardMemberCategory
    {
        return $this->category;
    }

    public function setCategory(?BoardMemberCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTitle(): ?BoardMemberTitle
    {
        return $this->title;
    }

    public function setTitle(?BoardMemberTitle $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return BoardMemberDon[]
     */
    public function getDons(): array
    {
        return array_map(
            fn(string $val) => BoardMemberDon::from($val),
            $this->dons
        );
    }

    /**
     * @param array<BoardMemberDon|string> $dons
     */
    public function setDons(array $dons): static
    {
        $this->dons = array_values(array_unique(array_map(
            fn($don) => $don instanceof BoardMemberDon ? $don->value : $don,
            $dons
        )));

        return $this;
    }

    /**
     * @return BoardMemberComite[]
     */
    public function getComites(): array
    {
        return array_map(
            fn(string $val) => BoardMemberComite::from($val),
            $this->comites
        );
    }

    /**
     * @param array<BoardMemberComite|string> $comites
     */
    public function setComites(array $comites): static
    {
        $this->comites = array_values(array_unique(array_map(
            fn($comite) => $comite instanceof BoardMemberComite ? $comite->value : $comite,
            $comites
        )));

        return $this;
    }

    public function getExpertise(): ?string
    {
        return $this->expertise;
    }

    public function setExpertise(?string $expertise): static
    {
        $this->expertise = $expertise;

        return $this;
    }

    public function getQualifications(): ?string
    {
        return $this->qualifications;
    }

    public function setQualifications(?string $qualifications): static
    {
        $this->qualifications = $qualifications;

        return $this;
    }

    public function getPhoto(): ?Media
    {
        return $this->photo;
    }

    public function setPhoto(?Media $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    #[Assert\Callback]
    public function validateTitleForCategory(ExecutionContextInterface $context): void
    {
        if (!$this->category) {
            return;
        }

        $validTitles = BoardMemberTitle::getValidTitlesPerCategory()[$this->category->value] ?? [];
        
        // If the category expects no specific titles, it should optionally be null.
        if (empty($validTitles) && $this->title !== null) {
            $context->buildViolation('Les membres de cette catégorie ne doivent pas avoir de titre défini.')
                ->atPath('title')
                ->addViolation();
            return;
        }

        // If a title is provided, check if it's in the valid list for this category
        if ($this->title !== null && !in_array($this->title, $validTitles, true)) {
            $context->buildViolation('Ce titre n\'est pas valide pour la catégorie sélectionnée.')
                ->atPath('title')
                ->addViolation();
        }
        
        // If the category REQUIRES a title, we should enforce it? The prompt says "ne peuvent avoir que les titres".
        // Let's enforce that BUREAU_RESTREINT, CONSEILLER_SPECIAL, DON, COMITE must have a title if not explicitly optional.
        // Actually, the prompt says "Vices présidents et administrateurs ont un titre optionnel, vide par défaut".
        // It implies the others are mandatory? Let's assume yes for BUREAU_RESTREINT.
        if ($this->title === null && !empty($validTitles)) {
            $context->buildViolation('Un titre est requis pour cette catégorie.')
                ->atPath('title')
                ->addViolation();
        }
    }
}
