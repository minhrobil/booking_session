<?php

namespace App\Service;

use App\Entity\Session;
use App\Entity\SessionTimeSlot;
use App\Repository\SessionRepository;
use App\Repository\SessionTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class SessionService
{
    private SessionRepository $sessionRepository;
    private SessionTypeRepository $sessionTypeRepository;
    private EntityManagerInterface $em;

    public function __construct(
        SessionRepository $sessionRepository,
        SessionTypeRepository $sessionTypeRepository,
        EntityManagerInterface $em
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->sessionTypeRepository = $sessionTypeRepository;
        $this->em = $em;
    }

    /**
     * @param \DateTimeInterface $date
     * @return Session[]
     */
    public function getOrCreateSessionsForDate(\DateTimeInterface $date): array
    {
        $existingSessions = $this->sessionRepository->findByDate($date);
        if (!empty($existingSessions)) {
            return $existingSessions;
        }
        return $this->em->getConnection()->transactional(function () use ($date) {
            

            $sessionTypes = $this->sessionTypeRepository->findAll();
            $newSessions = [];

            foreach ($sessionTypes as $sessionType) {
                $session = new Session();
                $session->setDate($date);
                $session->setSessionType($sessionType);

                $this->em->persist($session);

                $duration = $sessionType->getDuration();
                $startTime = (clone $date)->setTime(6, 0);
                $endTime = (clone $date)->setTime(22, 0);

                while ($startTime < $endTime) {
                    $slot = new SessionTimeSlot();
                    $slot->setSession($session);
                    $slot->setStartTime(clone $startTime);
                    $slotEndTime = (clone $startTime)->modify("+{$duration} minutes");
                    $slot->setEndTime($slotEndTime);
                    $slot->setIsBooked(false);
                    $this->em->persist($slot);
                    $startTime = $slotEndTime;
                    $session->addSessionTimeSlot($slot);
                }

                $newSessions[] = $session;
            }

            $this->em->flush();

            return $newSessions;
        });
    }

}
