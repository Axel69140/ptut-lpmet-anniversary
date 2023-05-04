<?php

namespace App\Entity;

use App\Repository\AnecdoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AnecdoteRepository::class)]
class Anecdote
{
    #[Groups(['user-return', 'anecdote-return'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user-return', 'anecdote-return'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[Groups(['user-return', 'anecdote-return'])]
    #[ORM\Column]
    private ?bool $isValidate = false;

    #[Groups(['anecdote-return'])]
    #[ORM\ManyToOne(inversedBy: 'anecdotes')]
    private ?User $creator = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIsValidate(): ?bool
    {
        return $this->isValidate;
    }

    public function setIsValidate(bool $isValidate): self
    {
        $this->isValidate = $isValidate;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $User): self
    {
        $this->creator = $User;

        return $this;
    }
}
