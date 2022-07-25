<?php

namespace App\Service;

use App\Handler\HandlerFactory;
use App\Handler\UnknownCommandHandler;
use App\Handler\GetBalanceHandler;
use TelegramBot\Api\Types\Update;

class BotCommandAnalyserService
{
    public function analyze(Update $update): string
    {
        // Message processing
        if (($message = $update->getMessage()) && ($text = trim($message->getText()))) {
            // extract params and command
            $params = explode(' ', $text);
            $command = array_shift($params);

            // is it a command
            if (isset(HandlerFactory::$handlers[$command])) {
                return $command;
            }

            // is it a wallet address
            if (preg_match('#^T([a-zA-Z0-9]{33})$#', $command)) {
                return GetBalanceHandler::KEY_COMMAND;
            }
        }

        return UnknownCommandHandler::KEY_COMMAND;
    }
}
