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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'sourceTask', targetEntity: Edge::class)]
    private Collection $outgoingEdges;

    #[ORM\OneToMany(mappedBy: 'targetTask', targetEntity: Edge::class)]
    private Collection $incomingEdges;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Diagram $pertChart = null;

    public function __construct()
    {
        $this->outgoingEdges = new ArrayCollection();
        $this->incomingEdges = new ArrayCollection();
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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
}
