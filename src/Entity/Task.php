<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"task", "userList"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\ExpressionLanguageSyntax(
     *     allowedVariables={"migration", "installation", "portabilité"}
     * )
     * @Groups({"task"})
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"task", "userList"})
     */
    private $difficulty;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"task",})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]{2}-[a-zA-Z]{4}-[a-zA-Z]{2}$/",
     *     message="The code must match the format xx-xxxx-xx"
     * )
     * @Groups({"task", "userList"})
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="task")
     * @Groups({"task"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"task"})
     */
    private $startDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }
}
