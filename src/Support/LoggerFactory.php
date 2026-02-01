<?php

namespace App\Support;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Processor\PsrLogMessageProcessor;

class LoggerFactory
{
    public static function create(string $channel = 'app'): Logger
    {
        $logger = new Logger($channel);

        $handler = new StreamHandler(__DIR__ . '/../../storage/logs/app.log', Level::Debug);

        $handler->pushProcessor(new PsrLogMessageProcessor());

        $logger->pushHandler($handler);

        return $logger;
    }
}
