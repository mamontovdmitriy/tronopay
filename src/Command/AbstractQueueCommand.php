<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractQueueCommand extends Command
{
    const FILE_PREFIX = 'queue';
    const WORKER_COMMAND = 'bot:queue:worker';

    const DELAY_LOOP  = 500000;  // 0.5
    const DELAY_EMPTY = 100000;  // 0.1
    const DELAY_ERROR = 1000000; // 1.0

    private array $commandWithParams = [];

    public function __construct(
        private bool $appDebug,
        private string $appPHP,
        private string $appEnv,
        private string $pathProject,
        private string $pathCache,
        string $name = null
    )
    {
        parent::__construct($name);

        $this->commandWithParams = $this->getCommandWithParams();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        set_time_limit(0);

        $phpProcessPid = getmypid();
        if (false === $phpProcessPid) {
            return $this->error('Cannot get pid. Process terminated');
        }

        $filenamePid = $this->getPidFilename();
        if (false === file_put_contents($filenamePid, $phpProcessPid)) {
            return $this->error('Cannot write pid in ' . $filenamePid);
        }

        $process = new Process($this->commandWithParams);
        $process->setTimeout(60)->enableOutput();

        while (true) {
            usleep(static::DELAY_LOOP);

            if (!$process->isRunning()) {
                try {
                    // Check our PID from the file, else it terminates
                    $newPid = file_get_contents($filenamePid);
                    if ($newPid != $phpProcessPid) {
                        return $this->error('New PID (' . $newPid . ') found. Process terminated. Pid=' . $phpProcessPid);
                    }

                    $process->start(function ($type, $buffer) use ($phpProcessPid) {
                        if ($buffer === 'EMPTY') {
                            if ($this->appDebug) {
                                $this->printMsg('Lock removed (autostop). Pid=' . $phpProcessPid);
                                exit(0);
                            }
                            // Delay if queue is empty
                            usleep(static::DELAY_EMPTY);
                        } elseif (!empty($buffer)) {
                            $this->printMsg('Command returns: ' . $buffer);
                            // Delay if it's error
                            usleep(static::DELAY_ERROR);
                        }
                    });
                } catch (\Exception $exception) {
                    $this->printMsg('Exception: ' . $exception->getMessage());
                    usleep(static::DELAY_ERROR);
                } finally {
                    gc_collect_cycles();
                }
            }
        }
    }

    private function getCommandWithParams(): array
    {
        $params = [];
        if (!$this->appDebug) {
            $params[] = 'nohup';
        }
        $params[] = $this->appPHP;
        $params[] = $this->pathProject . '/bin/console';
        $params[] = static::WORKER_COMMAND;
        $params[] = '--env=' . $this->appEnv;

        return $params;
    }

    private function getPidFilename(): string
    {
        return $this->pathCache . DIRECTORY_SEPARATOR . static::FILE_PREFIX . '.pid';
    }

    private function error(string $message)
    {
        $this->printMsg($message, true);

        return 0;
    }

    private function printMsg(string $message, bool $isError = false)
    {
        $message = (new \DateTime())->format('Y-m-d H:i:s.ms') . ' ' . static::FILE_PREFIX . ' ==> ' . $message;

        if ($this->appDebug) {
            echo ($isError ? '<error>' : '') . $message . PHP_EOL . ($isError ? '</error>' : '');
        }

        $messageLog = ($isError ? ' [ERROR] ' : '') . $message . PHP_EOL;
        @file_put_contents($this->pathCache . '/queue_cron.log', $messageLog, FILE_APPEND | LOCK_EX);
    }
}
