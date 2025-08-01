<?php

namespace App\Controller;

use App\DTO\BookingRequest;
use App\DTO\BookingItemRequest;
use App\Service\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookingController extends AbstractController
{
    private BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    #[Route('/api/bookings', name: 'createBooking', methods: ['POST', 'OPTIONS'])]
    public function createBooking(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->json(['error' => 'Invalid JSON'], 400);
            }

            $errors = $this->validateBookingRequest($data);
            if (!empty($errors)) {
                return $this->json(['errors' => $errors], 400);
            }

            // Map to DTO
            $bookingRequest = new BookingRequest();
            $bookingRequest->fullName = $data['fullName'];
            $bookingRequest->email = $data['email'];
            $bookingRequest->phoneNumber = $data['phoneNumber'];
            $bookingRequest->acceptedTerms = $data['acceptedTerms'];

            foreach ($data['bookings'] as $item) {
                $bookingItem = new BookingItemRequest();
                $bookingItem->sessionTimeSlotId = $item['sessionTimeSlotId'];
                $bookingItem->trainerId = $item['trainerId'];
                $bookingRequest->bookings[] = $bookingItem;
            }

            $this->bookingService->book($bookingRequest);

            return $this->json(['message' => 'Booking received!']);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Internal server error.',
                'details' => $e->getMessage(), 
            ], 500);
        }
    }

    private function validateBookingRequest(array $data): array
    {
        $errors = [];

        if (empty($data['fullName'])) {
            $errors[] = 'Full name is required.';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email is invalid.';
        }

        if (empty($data['phoneNumber'])) {
            $errors[] = 'Phone number is required.';
        }

        if (!isset($data['acceptedTerms']) || $data['acceptedTerms'] !== true) {
            $errors[] = 'You must accept the terms.';
        }

        if (empty($data['bookings']) || !is_array($data['bookings'])) {
            $errors[] = 'At least one booking item is required.';
        } else {
            foreach ($data['bookings'] as $index => $bookingItem) {
                if (empty($bookingItem['sessionTimeSlotId'])) {
                    $errors[] = "Booking item #" . ($index + 1) . ": sessionTimeSlotId is required.";
                }
                if (empty($bookingItem['trainerId'])) {
                    $errors[] = "Booking item #" . ($index + 1) . ": trainerId is required.";
                }
            }
        }

        return $errors;
    }
}
