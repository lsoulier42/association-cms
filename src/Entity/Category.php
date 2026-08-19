<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends AbstractEntity
{
    /**
     * @var string $name
     */
    #[ORM\Column(length: 255)]
    private string $name = '';

    /**
     * @var string $slug
     */
    #[ORM\Column(length: 255, unique: true)]
    private string $slug = '';

    /**
     * @var Collection<int, Article> $articles
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Article::class)]
    private Collection $articles;

    /**
     * @var Collection<int, SpecialPage> $specialPages
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: SpecialPage::class)]
    private Collection $specialPages;

    /**
     * @var int $menuOrder
     */
    #[ORM\Column(options: ['default' => 0])]
    private int $menuOrder = 0;

    public function __construct()
    {
        parent::__construct();
        $this->articles = new ArrayCollection();
        $this->specialPages = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    // ... (getName, setName, getSlug, setSlug remain)

    /**
     * @return Collection<int, SpecialPage>
     */
    public function getSpecialPages(): Collection
    {
        return $this->specialPages;
    }

    public function addSpecialPage(SpecialPage $specialPage): self
    {
        if (!$this->specialPages->contains($specialPage)) {
            $this->specialPages->add($specialPage);
            $specialPage->setCategory($this);
        }
        return $this;
    }

    public function removeSpecialPage(SpecialPage $specialPage): self
    {
        if ($this->specialPages->removeElement($specialPage)) {
            if ($specialPage->getCategory() === $this) {
                $specialPage->setCategory(null);
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @param Article $article
     * @return $this
     */
    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setCategory($this);
        }
        return $this;
    }

    /**
     * @param Article $article
     * @return $this
     */
    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }
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
