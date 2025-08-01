<?php

namespace App\Entity;

use App\Repository\BookingItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingItemRepository::class)]
class BookingItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Booking $booking = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionTimeSlot $sessionTimeSlot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): static
    {
        $this->booking = $booking;

        return $this;
    }

    public function getSessionTimeSlot(): ?SessionTimeSlot
    {
        return $this->sessionTimeSlot;
    }

    public function setSessionTimeSlot(?SessionTimeSlot $sessionTimeSlot): static
    {
        $this->sessionTimeSlot = $sessionTimeSlot;

        return $this;
    }
}
