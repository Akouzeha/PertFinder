<?php

namespace App\Entity;

use App\Entity\Comment;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Diagram::class)]
    private Collection $diagrams;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column]
    private ?bool $isLocked = null;

    public function __construct()
    {
        $this->diagrams = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

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
     * @return Collection<int, Diagram>
     */
    public function getDiagrams(): Collection
    {
        return $this->diagrams;
    }

    public function addDiagram(Diagram $diagram): static
    {
        if (!$this->diagrams->contains($diagram)) {
            $this->diagrams->add($diagram);
            $diagram->setProject($this);
        }

        return $this;
    }

    public function removeDiagram(Diagram $diagram): static
    {
        if ($this->diagrams->removeElement($diagram)) {
            // set the owning side to null (unless already changed)
            if ($diagram->getProject() === $this) {
                $diagram->setProject(null);
            }
        }

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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProject($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProject() === $this) {
                $comment->setProject(null);
            }
        }

        return $this;
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

    public function __toString(): string
    {
        return $this->title;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    
    public function getLastComment(): ?Comment
    {
        // Create a sorting criteria to get the comments in descending order by commentTime
        $criteria = Criteria::create()
            ->orderBy(['commentTime' => Criteria::DESC])
            ->setMaxResults(1);
        // Get the last comment using the sorting criteria
        $lastComment = $this->comments->matching($criteria)->first();
        return $lastComment ? $lastComment : null;

    }

    public function isIsLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): static
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    public function calculNumberDays(): int
    {
        $dateDebut = $this->getDateDebut();
        $dateFin = $this->getDateFin();
        $interval = $dateDebut->diff($dateFin);
        return $interval->days;
    }
    public function calculZscore(int $dureeProject, int $dureeDiagram, float $variance)
    {
        $zscore = 0;
        $zscore = ($dureeProject - $dureeDiagram) / sqrt($variance);
        return $zscore;
    }
    
    function calculateProbabilityAndNormalDistribution($zScore) {
        $pi = 3.141592653589793;
        $a1 =  0.254829592;
        $a2 = -0.284496736;
        $a3 =  1.421413741;
        $a4 = -1.453152027;
        $a5 =  1.061405429;
        $p  =  0.3275911;
    
        $sign = ($zScore < 0) ? -1 : 1;
        $x = abs($zScore);
    
        $t = 1.0 / (1.0 + $p * $x);
        $y = ((((($a5 * $t + $a4) * $t) + $a3) * $t + $a2) * $t + $a1) * $t;
    
        $erfResult = $sign * (1.0 - $y * exp(-$x * $x));
        
        $normalDistribution = 0.5 * (1 + $erfResult);
    
        $probability = $normalDistribution * 100; // Convert to percentage
    
        return $probability;
    }
    
}
