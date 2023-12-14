<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $commentTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

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


    public function getCommentTime(): ?\DateTimeInterface
    {
        return $this->commentTime;
    }
    
    public function setCommentTime(\DateTimeInterface $commentTime): static
    {
        $this->commentTime = $commentTime;
    
        return $this;
    }

    //time difference
    public function getTimeDifference(): string
    {
        $now = new \DateTime();
        $interval = $now->diff($this->getCommentTime());
        if ($interval->y > 0) {
            return 'il y a ' . $interval->y . 'ans'  ;
        } elseif ($interval->m > 0) {
            return 'il y a ' .$interval->m . 'mois' ;
        } elseif ($interval->d > 0) {
            return 'il y a ' . $interval->d . ' jours' ;
        } elseif ($interval->h > 0) {
            return 'il y a ' . $interval->h . ' heurs';
        } elseif ($interval->i > 0) {
            return 'il y a ' . $interval->i . ' minutes';
        } else {
            return 'il y a ' . $interval->s . ' seconds';
        }
    }

}
