<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendBookingConfirmation(string $to, string $fullName): void
    {
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($to)
            ->subject('Booking Confirmation')
            ->text("Dear $fullName,\n\nThank you for your booking.\n\nBest regards,\nBooking Team");

        $this->mailer->send($email);
    }
}
