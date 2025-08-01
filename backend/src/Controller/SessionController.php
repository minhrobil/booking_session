<?php

namespace App\Controller;

use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    #[Route('/api/sessions', name: 'search_sessions_by_date', methods: ['GET'])]
    public function searchByDate(Request $request): JsonResponse
    {
        $dateString = $request->query->get('date');

        if (!$dateString) {
            return $this->json(['error' => 'Missing "date" query parameter. Format: YYYY-MM-DD'], 400);
        }

        try {
            $date = new \DateTimeImmutable($dateString);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
        }

        $date = \DateTime::createFromImmutable($date);
        $sessions = $this->sessionService->getOrCreateSessionsForDate($date);

        $data = [];
        foreach ($sessions as $session) {
            $slots = [];
            foreach ($session->getSessionTimeSlots() as $slot) {
                $slots[] = [
                    'id' => $slot->getId(),
                    'start' => $slot->getStartTime()->format('H:i'),
                    'end' => $slot->getEndTime()->format('H:i'),
                    'trainer' => $slot->getTrainer()?->getName(),
                    'price' => $session->getSessionType()->getPrice(),
                    'isBooked' => $slot->isBooked(),
                ];
            }

            usort($slots, function ($a, $b) {
                return $a['id'] <=> $b['id'];
            });

            $data[] = [
                'sessionType' => $session->getSessionType()->getName(),
                'date' => $session->getDate()->format('Y-m-d'),
                'timeSlots' => $slots,
            ];
        }

        return $this->json($data);
    }
}
