<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Proxy;

use Pollen\Proxy\AbstractProxy;
use Pollen\WpPost\WpPostTypeInterface;
use Pollen\WpPost\WpPostTypeManagerInterface;

/**
 * @method static WpPostTypeInterface[]|array all()
 * @method static WpPostTypeInterface|null get(string $name)
 * @method static register(string $name, WpPostTypeInterface|array $args)
 */
class WpPostType extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return WpPostTypeManagerInterface
     */
    public static function getInstance(): WpPostTypeManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return WpPostTypeManagerInterface::class;
    }
}