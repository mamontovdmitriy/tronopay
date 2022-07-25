<?php

namespace App\Command;

use App\Handler\HandlerFactory;
use App\Service\BotClientService;
use App\Service\BotCommandAnalyserService;
use App\Service\BotLastMessageService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TelegramBot\Api\Types\Update;

class GetUpdatesCommand extends Command
{
    protected static $defaultName = 'bot:get-updates';
    protected static $defaultDescription = 'Getting new messages for bot';

    public function __construct(
        private BotClientService          $clientService,
        private BotCommandAnalyserService $commandAnalyserService,
        private BotLastMessageService     $lastMessageService,
        private HandlerFactory            $handlerFactory,
        string                            $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lastMessageId = $this->lastMessageService->getLastMessageId();

        $messages = $this->clientService->getUpdates(++$lastMessageId);
        if (empty($messages)) {
            echo 'EMPTY';

            return 0;
        }

        /** @var Update $message */
        foreach ($messages as $message) {
            try {
                $handlerType = $this->commandAnalyserService->analyze($message);

                $handler = $this->handlerFactory->create($handlerType);

                $handler->handle($message);

                $lastMessageId = $message->getUpdateId();
            } catch (\Exception $exception) {
                // continue
                dump($exception);
            }
        }

        $this->lastMessageService->saveLastMessageId($lastMessageId);

        return 0;
    }
}
