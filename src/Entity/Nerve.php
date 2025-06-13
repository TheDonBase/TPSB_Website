<?php

namespace App\Entity;

use App\Repository\NerveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NerveRepository::class)]
class Nerve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column]
    private ?int $nerve = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastUpdated = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $pastNerve = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getNerve(): ?int
    {
        return $this->nerve;
    }

    public function setNerve(int $nerve): static
    {
        $this->nerve = $nerve;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeImmutable
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(\DateTimeImmutable $lastUpdated): static
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    public function getPastNerve(): ?string
    {
        return $this->pastNerve;
    }

    public function setPastNerve(string $pastNerve): static
    {
        $this->pastNerve = $pastNerve;

        return $this;
    }
}
