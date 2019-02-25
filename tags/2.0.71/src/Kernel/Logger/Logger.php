<?php

namespace tiFy\Kernel\Logger;

use Illuminate\Support\Arr;
use Monolog\Logger as MonologLogger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\Kernel\Logger as LoggerContract;

class Logger extends MonologLogger implements LoggerContract
{
    /**
     * {@inheritdoc}
     */
    public function addSuccess($message, array $context = [])
    {
        return $this->addNotice($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public static function create($name = 'system', $attrs = [], AppInterface $app)
    {
        if ($app->bound("logger.item.{$name}")) :
            return $app->resolve("logger.item.{$name}");
        endif;

        $resolved = $app->singleton(
            "logger.item.{$name}",
            function($name) {
                return new static($name);
            })
            ->build([$name]);

        $resolved->parse($attrs);

        return $resolved;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $filename = Arr::get($attrs, 'filename')
            ? : paths()->getLogPath($this->getName() . '.log');

        $formatter = new LineFormatter(Arr::get($attrs, 'format', null));

        $stream = new RotatingFileHandler($filename, Arr::get($attrs, 'rotate', 10));
        $stream->setFormatter($formatter);

        if ($timezone = get_option('timezone_string')) :
            $this->setTimezone(new \DateTimeZone($timezone));
        endif;

        $this->pushHandler($stream);
    }

    /**
     * {@inheritdoc}
     */
    public function success($message, array $context = [])
    {
        return $this->addSuccess($message, $context);
    }
}