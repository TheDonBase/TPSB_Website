<?php

namespace App\Entity;

use App\Repository\BotStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BotStatusRepository::class)]
class BotStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastSeen = null;

    #[ORM\Column]
    private ?int $ping = null;

    #[ORM\Column(length: 255)]
    private ?string $version = null;

    #[ORM\Column(length: 255)]
    private ?string $currentActivity = null;

    #[ORM\Column]
    private ?float $memoryUsage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastSeen(): ?\DateTimeImmutable
    {
        return $this->lastSeen;
    }

    public function setLastSeen(\DateTimeImmutable $lastSeen): static
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    public function getPing(): ?int
    {
        return $this->ping;
    }

    public function setPing(int $ping): static
    {
        $this->ping = $ping;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getCurrentActivity(): ?string
    {
        return $this->currentActivity;
    }

    public function setCurrentActivity(string $currentActivity): static
    {
        $this->currentActivity = $currentActivity;

        return $this;
    }

    public function getMemoryUsage(): ?float
    {
        return $this->memoryUsage;
    }

    public function setMemoryUsage(float $memoryUsage): static
    {
        $this->memoryUsage = $memoryUsage;

        return $this;
    }
}
