<?php

namespace App\Service;

use IEXBase\TronAPI\TronAddress;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailService
{
    private MailerInterface $mailer;
    private TranslatorInterface $translator;
    private string $emailFrom;
    private string $appName;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator, string $emailFrom, string $appName)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->emailFrom = $emailFrom;
        $this->appName = $appName;
    }

    public function sendAccountInformation(string $emailAddress, TronAddress $tronAddress): void
    {
        $email = new TemplatedEmail();

        $from = Address::create($this->emailFrom);
        $messageId = md5($emailAddress) . '-' . $from->getAddress();
        $email->getHeaders()
            ->addIdHeader('Message-ID', $messageId)
            ->addTextHeader('References', $messageId)
            ->addTextHeader('In-Reply-To', $messageId);

        $subject = $this->translator->trans('email_subject', [
            '%app_name%' => $this->appName,
            '%date%'     => (new \DateTime('now', new \DateTimeZone("UTC")))->format('Y-m-d H:i:s (e)'),
        ]);
        $email
            ->to(Address::create($emailAddress))
            ->subject($subject)
            ->from($from)
            ->htmlTemplate('email/account-info.html.twig')
            ->context([
                'address' => $tronAddress,
            ]);

        $this->mailer->send($email);
    }
}
