<?php

namespace App\Entity;

use App\Repository\WebHookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WebHookRepository::class)
 */
class WebHook
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\ManyToMany(targetEntity=Cast::class, mappedBy="webhooks")
     */
    private $casts;

    public function __construct()
    {
        $this->casts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection|Cast[]
     */
    public function getCasts(): Collection
    {
        return $this->casts;
    }

    public function addCast(Cast $cast): self
    {
        if (!$this->casts->contains($cast)) {
            $this->casts[] = $cast;
            $cast->addWebhook($this);
        }

        return $this;
    }

    public function removeCast(Cast $cast): self
    {
        if ($this->casts->removeElement($cast)) {
            $cast->removeWebhook($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
