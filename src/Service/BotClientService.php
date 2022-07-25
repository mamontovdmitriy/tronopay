<?php

namespace App\Service;

use TelegramBot\Api\Client;

class BotClientService
{
    private Client $client;

    public function __construct(string $appTgToken)
    {
        $this->client = new Client($appTgToken);
    }

    public function getUpdates(int $lastMessageId): array
    {
        return $this->client->getUpdates($lastMessageId);
    }

    public function send(int $chatId, string $text, int $replyToMessageId = null, $replyMarkup = null)
    {
        return $this->client->sendMessage($chatId, $text, 'HTML', false, $replyToMessageId, $replyMarkup);
    }

    public function update(int $chatId, int $messageId, string $text, $replyMarkup = null)
    {
        $result = $this->client->editMessageText($chatId, $messageId, $text, 'HTML', false);

        if ($replyMarkup) {
            $result = $this->client->editMessageReplyMarkup($chatId, $messageId, $replyMarkup);
        }

        return $result;
    }
}
