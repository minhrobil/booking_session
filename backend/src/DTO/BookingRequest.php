<?php
namespace App\DTO;

class BookingRequest
{
    public ?string $fullName = null;
    public ?string $email = null;
    public ?string $phoneNumber = null;
    public ?bool $acceptedTerms = null;

    /** @var BookingItemRequest[] */
    public array $bookings = [];
}
