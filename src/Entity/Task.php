<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $optTime = null;

    #[ORM\Column]
    private ?int $pesTime = null;

    #[ORM\Column]
    private ?int $mosTime = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Diagram $pertChart = null;

    #[ORM\Column]
    private ?float $variance = null;

    #[ORM\ManyToMany(targetEntity: self::class)]
    private Collection $dependentTasks;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Edge::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $edges;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Edge::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $taskPredecessors;

    #[ORM\Column(nullable: true)]
    private ?int $level = null;

    public function __construct()
    {
        $this->dependentTasks = new ArrayCollection();
        $this->edges = new ArrayCollection();
        $this->taskPredecessors = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOptTime(): ?int
    {
        return $this->optTime;
    }

    public function setOptTime(int $optTime): static
    {
        $this->optTime = $optTime;

        return $this;
    }

    public function getPesTime(): ?int
    {
        return $this->pesTime;
    }

    public function setPesTime(int $pesTime): static
    {
        $this->pesTime = $pesTime;

        return $this;
    }

    public function getMosTime(): ?int
    {
        return $this->mosTime;
    }

    public function setMosTime(int $mosTime): static
    {
        $this->mosTime = $mosTime;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getPertChart(): ?Diagram
    {
        return $this->pertChart;
    }

    public function setPertChart(?Diagram $pertChart): static
    {
        $this->pertChart = $pertChart;

        return $this;
    }

    public function getVariance(): ?float
    {
        return $this->variance;
    }

    public function setVariance(float $variance): static
    {
        $this->variance = $variance;

        return $this;
    }

    public function calculateDuration(\DateTime $optTime, \DateTime $mosTime, \DateTime $pesTime): int
    {
        $dureeInSecond = ($optTime->getTimestamp() + 4 * $mosTime->getTimestamp() + $pesTime->getTimestamp()) / 6;
        $duree = round($dureeInSecond / 86400);
        return $duree;
    }

    /**
     * @return Collection<int, self>
     */
    public function getDependentTasks(): Collection
    {
        return $this->dependentTasks;
    }

    public function addDependentTask(Task $dependentTask): static
    {
        if (!$this->dependentTasks->contains($dependentTask)) {
            $this->dependentTasks->add($dependentTask);
        }

        return $this;
    }

    public function removeDependentTask(task $dependentTask): static
    {
        $this->dependentTasks->removeElement($dependentTask);

        return $this;
    }

    /**
     * @return Collection<int, Edge>
     */
    public function getEdges(): Collection
    {
        return $this->edges;
    }

    public function addEdge(Edge $edge): static
    {
        if (!$this->edges->contains($edge)) {
            $this->edges->add($edge);
            $edge->setTask($this);
        }

        return $this;
    }

    public function removeEdge(Edge $edge): static
    {
        if ($this->edges->removeElement($edge)) {
            // set the owning side to null (unless already changed)
            if ($edge->getTask() === $this) {
                $edge->setTask(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Edge>
     */
    public function getTaskPredecessors(): Collection
    {
        return $this->taskPredecessors;
    }

    public function addTaskPredecessor(Edge $taskPredecessor): static
    {
        if (!$this->taskPredecessors->contains($taskPredecessor)) {
            $this->taskPredecessors->add($taskPredecessor);
            $taskPredecessor->setPredecessor($this);
        }

        return $this;
    }

    public function removeTaskPredecessor(Edge $taskPredecessor): static
    {
        if ($this->taskPredecessors->removeElement($taskPredecessor)) {
            // set the owning side to null (unless already changed)
            if ($taskPredecessor->getPredecessor() === $this) {
                $taskPredecessor->setPredecessor(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): static
    {
        $this->level = $level;

        return $this;
    }

}
