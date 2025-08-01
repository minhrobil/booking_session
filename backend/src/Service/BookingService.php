<?php
namespace App\Service;

use App\Dto\BookingRequest;
use App\Repository\SessionTimeSlotRepository;
use App\Repository\TrainerRepository;
use App\Entity\Booking;
use App\Entity\BookingItem;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    private SessionTimeSlotRepository $slotRepo;
    private TrainerRepository $trainerRepo;
    private EntityManagerInterface $em;
    private MailerService $mailer;

    public function __construct(
        SessionTimeSlotRepository $slotRepo,
        TrainerRepository $trainerRepo,
        EntityManagerInterface $em,
        MailerService $mailer
    ) {
        $this->slotRepo = $slotRepo;
        $this->trainerRepo = $trainerRepo;
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function book(BookingRequest $request): void
    {
        $this->em->getConnection()->transactional(function () use ($request) {
            $booking = new Booking();
            $booking->setFullName($request->fullName);
            $booking->setEmail($request->email);
            $booking->setPhoneNumber($request->phoneNumber);
            $booking->setCreatedAt(new \DateTime());

            $this->em->persist($booking);

            foreach ($request->bookings as $item) {
                $slot = $this->slotRepo->find($item->sessionTimeSlotId);
                if (!$slot) {
                    throw new \InvalidArgumentException("Time slot ID {$item->sessionTimeSlotId} not found.");
                }

                if ($slot->isBooked()) {
                    $start = $slot->getStartTime()->format('H:i');
                    $end = $slot->getEndTime()->format('H:i');
                    $sessionTypeName = $slot->getSession()->getSessionType()->getName();
                    throw new \InvalidArgumentException("Time slot $start - $end of $sessionTypeName is already booked.");
                }

                $trainer = $this->trainerRepo->find($item->trainerId);
                if (!$trainer) {
                    throw new \InvalidArgumentException("Trainer not found for ID {$item->trainerId}.");
                }

                $slot->setTrainer($trainer);
                $slot->setIsBooked(true);

                $bookingItem = new BookingItem();
                $bookingItem->setBooking($booking);
                $bookingItem->setSessionTimeSlot($slot);

                $this->em->persist($bookingItem);
            }

            $this->em->flush();
            $this->mailer->sendBookingConfirmation($request->email, $request->fullName);
        });
    }
}