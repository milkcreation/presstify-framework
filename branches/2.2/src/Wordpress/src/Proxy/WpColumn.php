<?php

declare(strict_types=1);

namespace Pollen\Wordpress\Proxy;

use Pollen\Proxy\AbstractProxy;
use tiFy\Wordpress\Column\ColumnInterface;
use tiFy\Wordpress\Column\ColumnItemInterface;

/**
 * @method static add(string $screen, string $name, array|ColumnItemInterface $attrs)
 * @method static stack(string $screen, array[]|ColumnItemInterface[] $attrs)
 */
class WpColumn extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return ColumnInterface
     */
    public static function getInstance(): ColumnInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return 'wp.column';
    }
}