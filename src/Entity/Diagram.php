<?php

namespace App\Entity;

use App\Repository\DiagramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiagramRepository::class)]
class Diagram
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lienTelechargement = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'pertChart', targetEntity: Task::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $tasks;

    #[ORM\ManyToOne(inversedBy: 'diagrams')]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'diagrams')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?array $adjacencyMatrix = null;

    #[ORM\Column(length: 100)]
    private ?string $imgName = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column]
    private ?float $variance = null;

    #[ORM\Column]
    private ?int $dureeCritique = null;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLienTelechargement(): ?string
    {
        return $this->lienTelechargement;
    }

    public function setLienTelechargement(?string $lienTelechargement): static
    {
        $this->lienTelechargement = $lienTelechargement;

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
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setPertChart($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getPertChart() === $this) {
                $task->setPertChart(null);
            }
        }

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getAdjacencyMatrix(): ?array
    {
        return $this->adjacencyMatrix;
    }

    public function setAdjacencyMatrix(?array $adjacencyMatrix): static
    {
        $this->adjacencyMatrix = $adjacencyMatrix;

        return $this;
    }

    public function getImgName(): ?string
    {
        return $this->imgName;
    }

    public function setImgName(string $imgName): static
    {
        $this->imgName = $imgName;

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

    public function getVariance(): ?float
    {
        return $this->variance;
    }

    public function setVariance(float $variance): static
    {
        $this->variance = $variance;

        return $this;
    }

    public function getDureeCritique(): ?int
    {
        return $this->dureeCritique;
    }

    public function setDureeCritique(int $dureeCritique): static
    {
        $this->dureeCritique = $dureeCritique;

        return $this;
    }

    
}
