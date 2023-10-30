<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */

 
class User implements UserInterface , PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"users"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *  @Groups({"users"})
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=false)
     *  @Groups({"users"})
     * @Assert\NotBlank()
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *  @Groups({"users"})
     *  @Assert\Expression("this.getLevel() in ['deb', 'inter', 'expert']", message="Invalid level")
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "Your first name must be at least {{ limit }} characters long"
     * )
     */

    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="user")
     */
    private $task;

    /**
     * @ORM\Column(type="string", length=191, nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Unique
     */
    private $username;

     /**
     * @ORM\Column(type="json")
     */
    private $roles = [];


    public function __construct()
    {
        $this->task = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTask(): Collection
    {
        return $this->task;
    }

    public function addTask(Task $task): self
    {
        if (!$this->task->contains($task)) {
            $this->task[] = $task;
            $task->setUser($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->task->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }

        return $this;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
 
        return array_unique($roles);
    }
 
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt(): ?string
    {
            return null;
    }

    public function eraseCredentials()
    {
    
    }
    
    public function getUserIdentifier(): string
    {
        return $this->getUsername() ; 
    }
}
