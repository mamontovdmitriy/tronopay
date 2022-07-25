<?php

namespace App\Handler;

use App\Service\BotClientService;
use App\Service\WalletService;
use Symfony\Contracts\Translation\TranslatorInterface;
use TelegramBot\Api\Types\Update;
use Twig\Environment;

class CreateAddressCommandHandler extends AbstractHandler
{
    const KEY_COMMAND = '/new';

    public function __construct(
        private BotClientService    $botService,
        private Environment         $template,
        private TranslatorInterface $translator,
        private WalletService       $walletService,
    )
    {
        parent::__construct($botService, $template, $translator);
    }

    public function handle(Update $update): void
    {
        $locale = $this->getLocale($update->getMessage()->getFrom()->getLanguageCode());
        $message = $this->getTemplate()->render('bot/create_address.html.twig', [
            'locale'  => $locale,
            'address' => $this->walletService->createAddress(),
        ]);
        $this->getBotService()->send($update->getMessage()->getChat()->getId(), $message);
    }
}
