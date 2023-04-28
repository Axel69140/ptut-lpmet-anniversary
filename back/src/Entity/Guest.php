<?php

namespace App\Entity;

use App\Repository\GuestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GuestRepository::class)]
class Guest
{
    #[Groups(['guest-return', 'user-return', 'participant-return'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['guest-return', 'user-return', 'participant-return'])]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Groups(['guest-return', 'user-return', 'participant-return'])]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[Groups(['guest-return', 'user-return', 'participant-return'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[Groups(['guest-return', 'participant-return'])]
    #[ORM\ManyToOne(inversedBy: 'guests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $invitedBy = null;



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

    public function getInvitedBy(): ?User
    {
        return $this->invitedBy;
    }

    public function setInvitedBy(?User $invitedBy): self
    {
        $this->invitedBy = $invitedBy;

        return $this;
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
}
