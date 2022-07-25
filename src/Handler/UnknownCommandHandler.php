<?php

namespace App\Handler;

use TelegramBot\Api\Types\Update;

class UnknownCommandHandler extends AbstractHandler
{
    const KEY_COMMAND = 'unknown_command';

    public function handle(Update $update): void
    {
        $response = $update->getMessage() ?: $update->getCallbackQuery();
        $locale = $this->getLocale($response->getFrom()->getLanguageCode());

        $chatId = $update->getMessage()
            ? $update->getMessage()->getChat()->getId()
            : $update->getCallbackQuery()->getMessage()->getChat()->getId();
        $message = $this->getTemplate()->render('bot/unknown_command.html.twig', ['locale' => $locale]);

        $this->getBotService()->send($chatId, $message);
    }
}
