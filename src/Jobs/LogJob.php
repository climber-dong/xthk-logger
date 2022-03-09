<?php

/*
 * This file is part of the Xthk/laravel-logger.
 *
 * (c) Xthk <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xthk\Logger\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;
use Psr\Log\LoggerInterface;

class LogJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    private $context;
    private $message;
    private $serverData;

    public function __construct(string $message, array $context = null, array $serverData = null)
    {
        $this->message = $message;
        $this->context = $context;
        $this->serverData = $serverData;
    }

    public function handle()
    {
        app()->forgetInstance(LoggerInterface::class);
        // unset(app()[LoggerInterface::class]);

        $logger = app(LoggerInterface::class)->channel('xthkFile')->getLogger();

        if ($logger instanceof Logger) {
            $logger->pushProcessor(new WebProcessor($this->serverData));
        }

        $logger->debug($this->message, $this->context);
    }
}
