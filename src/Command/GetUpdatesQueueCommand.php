<?php

namespace App\Command;

/**
 * Class GetUpdatesQueueCommand
 */
class GetUpdatesQueueCommand extends AbstractQueueCommand
{
    public const FILE_PREFIX = 'queue.get-updates';
    public const WORKER_COMMAND = 'bot:get-updates';

    public const DELAY_LOOP = 250000;
    public const DELAY_EMPTY = 250000;
    public const DELAY_ERROR = 100000;

    protected static $defaultName = 'bot:queue:get-updates';
    protected static $defaultDescription = 'Daemon GetUpdates';
}
