<?php

namespace tiFy\Kernel\Logger;

use Monolog\Logger as MonologLogger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class Logger extends MonologLogger
{
    /**
     * Classe de rappel du controleur de journalisation global.
     * @return self
     */
    protected static $globalLogger;

    /**
     * Définition du controleur de journalisation global.
     * @return self
     */
    public static function globalReport()
    {
        if ($logger = self::$globalLogger) :
            return $logger;
        endif;

        $filename = WP_CONTENT_DIR . '/uploads/tiFy.log';

        $formatter = new LineFormatter();
        $stream = new RotatingFileHandler($filename, 7);
        $stream->setFormatter($formatter);

        $logger = new self('tiFy');

        if ($timezone = get_option('timezone_string')) :
            $logger->setTimezone(new \DateTimeZone($timezone));
        endif;
        $logger->pushHandler($stream);

        return self::$globalLogger = $logger;
    }
}