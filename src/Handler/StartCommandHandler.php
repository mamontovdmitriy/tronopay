<?php

namespace App\Handler;

use TelegramBot\Api\Types\Update;

class StartCommandHandler extends AbstractHandler
{
    const KEY_COMMAND = '/start';

    public function handle(Update $update): void
    {
        $locale = $this->getLocale($update->getMessage()->getFrom()->getLanguageCode());

        $message = $this->getTemplate()->render('bot/start.html.twig', ['locale' => $locale]);

        $this->getBotService()->send($update->getMessage()->getChat()->getId(), $message);
    }
}
