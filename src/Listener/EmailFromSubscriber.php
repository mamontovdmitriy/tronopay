<?php

namespace App\Listener;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailFromSubscriber implements EventSubscriberInterface
{
    private string $emailFrom;

    public function __construct(string $emailFrom)
    {
        $this->emailFrom = $emailFrom;
    }

    #[ArrayShape([MessageEvent::class => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }

    public function onMessage(MessageEvent $event)
    {
        $email = $event->getMessage();
        if (!$email instanceof Email) {
            return;
        }

        $email->from(Address::create($this->emailFrom));
    }
}
