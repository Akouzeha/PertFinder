<?php

namespace App\Entity;

use App\Repository\EdgeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EdgeRepository::class)]
class Edge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'outgoingEdges')]
    private ?Task $sourceTask = null;

    #[ORM\ManyToOne(inversedBy: 'incomingEdges')]
    private ?Task $targetTask = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSourceTask(): ?Task
    {
        return $this->sourceTask;
    }

    public function setSourceTask(?Task $sourceTask): static
    {
        $this->sourceTask = $sourceTask;

        return $this;
    }

    public function getTargetTask(): ?Task
    {
        return $this->targetTask;
    }

    public function setTargetTask(?Task $targetTask): static
    {
        $this->targetTask = $targetTask;

        return $this;
    }
}
