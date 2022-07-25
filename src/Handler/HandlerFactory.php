<?php

namespace App\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;

class HandlerFactory
{
    public function __construct(private ContainerInterface $container)
    {
    }

    static public array $handlers = [
        StartCommandHandler::KEY_COMMAND         => StartCommandHandler::class,
        GetBalanceHandler::KEY_COMMAND           => GetBalanceHandler::class,
        CreateAddressCommandHandler::KEY_COMMAND => CreateAddressCommandHandler::class,
        UnknownCommandHandler::KEY_COMMAND       => UnknownCommandHandler::class,
    ];

    public function create(?string $command): AbstractHandler
    {
        if (!isset(self::$handlers[$command])) {
            throw new \RuntimeException(sprintf('"%s" command handler undefined!', $command));
        }

        $handler = $this->container->get(self::$handlers[$command]);
        if (!$handler instanceof AbstractHandler) {
            throw new \RuntimeException(sprintf('"%s" handler not found!', $command));
        }

        return $handler;
    }
}
