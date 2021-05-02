<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Pollen\Session\SessionManagerInterface;

/**
 *
 */
class Session extends AbstractProxy
{
    /**
     * @inheritDoc
     *
     * @return SessionManagerInterface
     */
    public static function getInstance(): SessionManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return SessionManagerInterface::class;
    }
}