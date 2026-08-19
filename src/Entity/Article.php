<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article extends AbstractEntity
{
    /**
     * @var string $title
     */
    #[ORM\Column(length: 255)]
    private string $title;

    /**
     * @var string $content
     */
    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    /**
     * @var DateTimeImmutable $publishedAt
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $publishedAt;

    /**
     * @var Category|null $category
     */
    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var bool $showInMenu
     */
    #[ORM\Column(options: ['default' => false])]
    private bool $showInMenu = false;

    /**
     * @var int $menuOrder
     */
    #[ORM\Column(options: ['default' => 0])]
    private int $menuOrder = 0;

    public function __construct()
    {
        parent::__construct();
        $this->publishedAt = new DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTimeImmutable $publishedAt
     * @return $this
     */
    public function setPublishedAt(DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return $this
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowInMenu(): bool
    {
        return $this->showInMenu;
    }

    /**
     * @param bool $showInMenu
     * @return $this
     */
    public function setShowInMenu(bool $showInMenu): self
    {
        $this->showInMenu = $showInMenu;
        return $this;
    }

    /**
     * @return int
     */
    public function getMenuOrder(): int
    {
        return $this->menuOrder;
    }

    /**
     * @param int $menuOrder
     * @return $this
     */
    public function setMenuOrder(int $menuOrder): self
    {
        $this->menuOrder = $menuOrder;
        return $this;
    }
}
