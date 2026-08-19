<?php

namespace App\Entity;

use App\Repository\LinkedInPostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkedInPostRepository::class)]
class LinkedInPost extends AbstractEntity
{
    /**
     * @var string $embedLink
     */
    #[ORM\Column(length: 500)]
    private string $embedLink;

    /**
     * @var string $title
     */
    #[ORM\Column(length: 255)]
    private string $title;

    /**
     * @return string
     */
    public function getEmbedLink(): string
    {
        return $this->embedLink;
    }

    /**
     * @param string $embedLink
     * @return $this
     */
    public function setEmbedLink(string $embedLink): self
    {
        $this->embedLink = $embedLink;
        return $this;
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
}
