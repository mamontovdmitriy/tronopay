<?php

namespace App\Handler;

use App\Service\BotClientService;

use Symfony\Contracts\Translation\TranslatorInterface;
use TelegramBot\Api\Types\Update;
use Twig\Environment;

abstract class AbstractHandler
{
    const KEY_COMMAND = '';

    public function __construct(
        private BotClientService    $botService,
        private Environment         $template,
        private TranslatorInterface $translator
    )
    {
    }

    abstract public function handle(Update $update): void;

    public function getBotService(): BotClientService
    {
        return $this->botService;
    }

    public function getTemplate(): Environment
    {
        return $this->template;
    }

    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    public function getLocale(string $locale): string
    {
        $globals = $this->template->getGlobals();
        $locales = $globals['locales'] ?? 'en';
        $localeList = explode('|', $locales);

        return \in_array($locale, $localeList) ? $locale : 'en';
    }
}
