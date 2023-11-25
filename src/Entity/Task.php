<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\OneToMany(mappedBy: 'sourceTask', targetEntity: Edge::class)]
    private Collection $outgoingEdges;

    #[ORM\OneToMany(mappedBy: 'targetTask', targetEntity: Edge::class)]
    private Collection $incomingEdges;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Diagram $pertChart = null;

    #[ORM\Column]
    private ?float $variance = null;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'tasks')]
    private Collection $dependancies;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'dependancies')]
    private Collection $tasks;

    public function __construct()
    {
        $this->outgoingEdges = new ArrayCollection();
        $this->incomingEdges = new ArrayCollection();
        $this->dependancies = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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

    /**
     * @return Collection<int, Edge>
     */
    public function getOutgoingEdges(): Collection
    {
        return $this->outgoingEdges;
    }

    public function addOutgoingEdge(Edge $outgoingEdge): static
    {
        if (!$this->outgoingEdges->contains($outgoingEdge)) {
            $this->outgoingEdges->add($outgoingEdge);
            $outgoingEdge->setSourceTask($this);
        }

        return $this;
    }

    public function removeOutgoingEdge(Edge $outgoingEdge): static
    {
        if ($this->outgoingEdges->removeElement($outgoingEdge)) {
            // set the owning side to null (unless already changed)
            if ($outgoingEdge->getSourceTask() === $this) {
                $outgoingEdge->setSourceTask(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Edge>
     */
    public function getIncomingEdges(): Collection
    {
        return $this->incomingEdges;
    }

    public function addIncomingEdge(Edge $incomingEdge): static
    {
        if (!$this->incomingEdges->contains($incomingEdge)) {
            $this->incomingEdges->add($incomingEdge);
            $incomingEdge->setTargetTask($this);
        }

        return $this;
    }

    public function removeIncomingEdge(Edge $incomingEdge): static
    {
        if ($this->incomingEdges->removeElement($incomingEdge)) {
            // set the owning side to null (unless already changed)
            if ($incomingEdge->getTargetTask() === $this) {
                $incomingEdge->setTargetTask(null);
            }
        }

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
    public function getDependancies(): Collection
    {
        return $this->dependancies;
    }

    public function addDependancy(self $dependancy): static
    {
        if (!$this->dependancies->contains($dependancy)) {
            $this->dependancies->add($dependancy);
        }

        return $this;
    }

    public function removeDependancy(self $dependancy): static
    {
        $this->dependancies->removeElement($dependancy);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(self $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->addDependancy($this);
        }

        return $this;
    }

    public function removeTask(self $task): static
    {
        if ($this->tasks->removeElement($task)) {
            $task->removeDependancy($this);
        }

        return $this;
    }
}
