<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Proxy;

use Pollen\Proxy\AbstractProxy;
use Pollen\WpUser\WpUserRoleInterface;
use Pollen\WpUser\WpUserRoleManagerInterface;

/**
 * @method static WpUserRoleInterface[]|array all()
 * @method static WpUserRoleInterface|null get(string $name)
 * @method static register(string $name, WpUserRoleInterface|array $args)
 */
class WpUserRole extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return WpUserRoleManagerInterface
     */
    public static function getInstance(): WpUserRoleManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return WpUserRoleManagerInterface::class;
    }
}