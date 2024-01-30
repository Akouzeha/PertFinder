<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['message'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['message'])]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sentAt = null;

    #[ORM\ManyToOne(inversedBy: 'sentMessages')]
    #[Groups(['message'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\Column]
    private ?bool $ansewred = null;


    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'responses', cascade: ['persist'])]
    private ?self $parentMessage = null;

    #[ORM\OneToMany(mappedBy: 'parentMessage', targetEntity: self::class, cascade: ['persist'])]
    private Collection $responses;

    public function __construct()
    {
        $this->responses = new ArrayCollection();
    }

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

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): static
    {
        $this->sentAt = $sentAt;

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

    public function __toString(): string
    {
        return $this->contenu;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): static
    {
        $this->sujet = $sujet;

        return $this;
    }
    public function messageTime(): string
    {
        $now = new \DateTime();
        $interval = $this->sentAt->diff($now);
        $days = $interval->format('%a');
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        
        if ($days == 0 && $hours == 0 && $minutes < 10) {
            return 'à l\'instant';
        }
        
        if ($days == 0 && $hours == 0) {
            return 'il y a ' . $minutes . ' minutes';
        }
        
        if ($days == 0 && $hours < 24) {
            return 'il y a ' . $hours . ' heures';
        }
        
        if ($days < 7) {
            return 'il y a ' . $days . ' jours';
        }
        //else retuen the sate of the message without hours and minutes
        return $this->sentAt->format('d/m/Y');

    }

    public function isAnsewred(): ?bool
    {
        return $this->ansewred;
    }

    public function setAnsewred(bool $ansewred): static
    {
        $this->ansewred = $ansewred;

        return $this;
    }

    public function getParentMessage(): ?self
    {
        return $this->parentMessage;
    }

    public function setParentMessage(?self $parentMessage): static
    {
        $this->parentMessage = $parentMessage;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(self $response): static
    {
        if (!$this->responses->contains($response)) {
            $this->responses->add($response);
            $response->setParentMessage($this);
        }

        return $this;
    }

    public function removeResponse(self $response): static
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getParentMessage() === $this) {
                $response->setParentMessage(null);
            }
        }

        return $this;
    }
}
