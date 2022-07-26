<?php

namespace App\Handler;

use App\Service\BotClientService;
use App\Service\BotWalletService;
use Symfony\Contracts\Translation\TranslatorInterface;
use TelegramBot\Api\Types\Update;
use Twig\Environment;

class GetBalanceHandler extends AbstractHandler
{
    const KEY_COMMAND = 'balance';

    public function __construct(
        private BotClientService    $botService,
        private Environment         $template,
        private TranslatorInterface $translator,
        private BotWalletService    $walletService,
    )
    {
        parent::__construct($this->botService, $this->template, $this->translator);
    }


    public function handle(Update $update): void
    {
        $locale = $this->getLocale($update->getMessage()->getFrom()->getLanguageCode());
        $address = trim($update->getMessage()->getText());

        $message = $this->getTemplate()->render('bot/get_balance.html.twig', [
            'locale'  => $locale,
            'address' => $address,
            'balance' => $this->walletService->getBalance($address),
        ]);

        $this->getBotService()->send($update->getMessage()->getChat()->getId(), $message);
    }
}
