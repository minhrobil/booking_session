<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionType $sessionType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    /**
     * @var Collection<int, SessionTimeSlot>
     */
    #[ORM\OneToMany(targetEntity: SessionTimeSlot::class, mappedBy: 'session', cascade: ['persist'], orphanRemoval: true)]
    private Collection $sessionTimeSlots;

    public function __construct()
    {
        $this->sessionTimeSlots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getSessionType(): ?SessionType
    {
        return $this->sessionType;
    }

    public function setSessionType(?SessionType $sessionType): static
    {
        $this->sessionType = $sessionType;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, SessionTimeSlot>
     */
    public function getSessionTimeSlots(): Collection
    {
        return $this->sessionTimeSlots;
    }

    public function addSessionTimeSlot(SessionTimeSlot $sessionTimeSlot): static
    {
        if (!$this->sessionTimeSlots->contains($sessionTimeSlot)) {
            $this->sessionTimeSlots->add($sessionTimeSlot);
            $sessionTimeSlot->setSession($this);
        }

        return $this;
    }

    public function removeSessionTimeSlot(SessionTimeSlot $sessionTimeSlot): static
    {
        if ($this->sessionTimeSlots->removeElement($sessionTimeSlot)) {
            // set the owning side to null (unless already changed)
            if ($sessionTimeSlot->getSession() === $this) {
                $sessionTimeSlot->setSession(null);
            }
        }

        return $this;
    }
}
