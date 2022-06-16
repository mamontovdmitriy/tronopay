<?php

namespace App\Service;

use IEXBase\TronAPI\TronAddress;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bridge\Twig\Mime\WrappedTemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendAccountInformation(string $emailAddress, TronAddress $tronAddress): void
    {
        $email = new TemplatedEmail();

        $email
            ->to(Address::create($emailAddress))
            ->subject('Account info')
            ->htmlTemplate('email/account-info.html.twig')
            ->context([
                'address' => $tronAddress,
            ]);

        $this->mailer->send($email);
    }
}
