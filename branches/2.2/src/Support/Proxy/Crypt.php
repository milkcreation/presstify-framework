<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Pollen\Encryption\EncrypterInterface;

/**
 * @method static mixed decrypt(string $hash)
 * @method static string encrypt(string $plain)
 */
class Crypt extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return EncrypterInterface
     */
    public static function getInstance(): EncrypterInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return EncrypterInterface::class;
    }
}