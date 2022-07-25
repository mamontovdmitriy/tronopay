<?php

namespace App\Handler;

use TelegramBot\Api\Types\Update;

class GetBalanceHandler extends AbstractHandler
{
    const KEY_COMMAND = 'balance';

    public function handle(Update $update): void
    {
        $locale = $this->getLocale($update->getMessage()->getFrom()->getLanguageCode());
        $address = trim($update->getMessage()->getText());

        $message = $this->getTemplate()->render('bot/get_balance.html.twig', [
            'locale'  => $locale,
            'address' => $address,
        ]);

        $this->getBotService()->send($update->getMessage()->getChat()->getId(), $message);
    }
}
