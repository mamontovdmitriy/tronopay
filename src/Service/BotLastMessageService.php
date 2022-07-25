<?php

namespace App\Service;

class BotLastMessageService
{
    const FILENAME_LAST_MESSAGE = 'last_message.txt';

    public function __construct(private string $pathCache)
    {
    }

    public function getLastMessageId(): int
    {
        $filename = $this->getFilename();
        if (!file_exists($filename)) {
            $this->saveLastMessageId(0);
        }

        return (int)file_get_contents($filename);
    }

    public function saveLastMessageId(int $id): void
    {
        file_put_contents($this->getFilename(), $id);
    }

    private function getFilename(): string
    {
        return $this->pathCache . '/' . self::FILENAME_LAST_MESSAGE;
    }
}