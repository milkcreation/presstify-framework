<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use DateTimeZone;
use Pollen\Log\LoggerInterface;
use Pollen\Log\LogManagerInterface;

/**
 * @method static LogManagerInterface|LoggerInterface addChannel(LoggerInterface $channel)
 * @method static bool addRecord(int $level, string $message, array $context = [])
 * @method static void alert(string $message, array $context = [])
 * @method static LoggerInterface channel(string $name = null)
 * @method static void close()
 * @method static void critical(string $message, array $context = [])
 * @method static void debug(string $message, array $context = [])
 * @method static void emergency(string $message, array $context = [])
 * @method static void error(string $message, array $context = [])
 * @method static LoggerInterface getDefault()
 * @method static array getHandlers()
 * @method static string getName()
 * @method static array getProcessors()
 * @method static DateTimeZone getTimezone()
 * @method static void notice(string $message, array $context = [])
 * @method static void info(string $message, array $context = [])
 * @method static bool isHandling(int $level)
 * @method static void log($level, string $message, array $context = [])
 * @method static callable popProcessor()
 * @method static LogManagerInterface|LoggerInterface pushProcessor(callable $callback)
 * @method static LoggerInterface|null registerChannel(string $name, array $params = [])
 * @method static void reset()
 * @method static LogManagerInterface setDefault(LoggerInterface $default)
 * @method static LogManagerInterface|LoggerInterface setHandlers(array $handlers)
 * @method static LogManagerInterface|LoggerInterface setTimezone(DateTimeZone $tz)
 * @method static void success(string $message, array $context = [])
 * @method static void useMicrosecondTimestamps(bool $micro)
 * @method static void warning(string $message, array $context = [])
 * @method static LogManagerInterface|LoggerInterface withName(string $name)
 */
class Log extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return LogManagerInterface
     */
    public static function getInstance(): LogManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return LogManagerInterface::class;
    }
}