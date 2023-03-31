<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $maxNumberGuests = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $allowedFunctions = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaxNumberGuests(): ?int
    {
        return $this->maxNumberGuests;
    }

    public function setMaxNumberGuests(int $maxNumberGuests): self
    {
        $this->maxNumberGuests = $maxNumberGuests;

        return $this;
    }

    public function getAllowedFunctions(): array
    {
        return $this->allowedFunctions;
    }

    public function setAllowedFunctions(array $allowedFunctions): self
    {
        $this->allowedFunctions = $allowedFunctions;

        return $this;
    }
}
