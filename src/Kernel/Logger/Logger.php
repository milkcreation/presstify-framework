<?php

namespace tiFy\Kernel\Logger;

use Illuminate\Support\Arr;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MonologLogger;
use tiFy\Contracts\Kernel\Logger as LoggerContract;

class Logger extends MonologLogger implements LoggerContract
{
    /**
     * @inheritdoc
     */
    public function addSuccess($message, array $context = [])
    {
        return $this->addNotice($message, $context);
    }

    /**
     * @inheritdoc
     */
    public static function create($name = 'system', $attrs = [])
    {
        if (!app()->has("logger.item.{$name}")) :
            app()->share("logger.item.{$name}", (new static($name))->parse($attrs));
        endif;

        return app()->get("logger.item.{$name}");
    }

    /**
     * @inheritdoc
     */
    public function parse($attrs = [])
    {
        $filename = Arr::get($attrs, 'filename')
            ?: paths()->getLogPath($this->getName() . '.log');

        $formatter = new LineFormatter(Arr::get($attrs, 'format', null));

        $stream = new RotatingFileHandler($filename, Arr::get($attrs, 'rotate', 10));
        $stream->setFormatter($formatter);

        if ($timezone = get_option('timezone_string')) :
            $this->setTimezone(new \DateTimeZone($timezone));
        endif;

        $this->pushHandler($stream);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function success($message, array $context = [])
    {
        return $this->addSuccess($message, $context);
    }
}