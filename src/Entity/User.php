<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $azureId;

    /**
     * @ORM\OneToMany(targetEntity=AssociationMember::class, mappedBy="user")
     */
    private $associationMembers;

    /**
     * @ORM\OneToMany(targetEntity=Cast::class, mappedBy="author")
     */
    private $casts;

    public function __construct()
    {
        $this->associationMembers = new ArrayCollection();
        $this->casts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAzureId(): ?string
    {
        return $this->azureId;
    }

    public function setAzureId(string $azureId): self
    {
        $this->azureId = $azureId;

        return $this;
    }

    /**
     * @return Collection|AssociationMember[]
     */
    public function getAssociationMembers(): Collection
    {
        return $this->associationMembers;
    }

    public function addAssociationMember(AssociationMember $associationMember): self
    {
        if (!$this->associationMembers->contains($associationMember)) {
            $this->associationMembers[] = $associationMember;
            $associationMember->setUser($this);
        }

        return $this;
    }

    public function removeAssociationMember(AssociationMember $associationMember): self
    {
        if ($this->associationMembers->removeElement($associationMember)) {
            // set the owning side to null (unless already changed)
            if ($associationMember->getUser() === $this) {
                $associationMember->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString(): string
    {
        return $this->getEmail();
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
            $cast->setAuthor($this);
        }

        return $this;
    }

    public function removeCast(Cast $cast): self
    {
        if ($this->casts->removeElement($cast)) {
            // set the owning side to null (unless already changed)
            if ($cast->getAuthor() === $this) {
                $cast->setAuthor(null);
            }
        }

        return $this;
    }
}
