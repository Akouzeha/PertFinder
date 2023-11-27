<?php

namespace App\Entity;

use App\Repository\TaskDependencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskDependencyRepository::class)]
class TaskDependency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Task $task = null;

    #[ORM\ManyToOne]
    private ?Task $dependentTask = null;

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

    public function getDependentTask(): ?Task
    {
        return $this->dependentTask;
    }

    public function setDependentTask(?Task $dependentTask): static
    {
        $this->dependentTask = $dependentTask;

        return $this;
    }
}
