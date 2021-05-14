<?php

declare(strict_types=1);

namespace tiFy\Proxy;

use Pollen\Proxy\AbstractProxy;
use tiFy\Contracts\Console\Console as ConsoleContract;
use tiFy\Contracts\Console\Command;
use tiFy\Contracts\Console\CommandStack;

/**
 * @method static Command add(Command $command)
 * @method static CommandStack stack(string $name, string[] $commands)
 */
class Console extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|ConsoleContract
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return 'console';
    }
}