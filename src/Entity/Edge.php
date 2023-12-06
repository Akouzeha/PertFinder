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

    //outgoing edges from the task
    #[ORM\ManyToOne(inversedBy: 'edges')]
    private ?Task $task = null;

    //incoming edges to the task
    #[ORM\ManyToOne(inversedBy: 'taskPredecessor')]
    private ?Task $predecessor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getPredecessor(): ?Task
    {
        return $this->predecessor;
    }

    public function setPredecessor(?Task $predecessor): static
    {
        $this->predecessor = $predecessor;

        return $this;
    }

    public function __toString(): string
    {
        return $this->task->getName() . ' -> ' . $this->predecessor->getName();
    }
}
