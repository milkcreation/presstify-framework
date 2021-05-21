<?php

declare(strict_types=1);

namespace tiFy\Proxy;

use Pollen\Proxy\AbstractProxy;
use Pollen\Http\RedirectResponseInterface;
use tiFy\Contracts\Routing\Redirector;

/**
 * @method static RedirectResponseInterface to(string $path, int $status = 302, array $headers = [])
 * @method static RedirectResponseInterface route(string $name, array $parameters = [], int $status = 302, array $headers = [])
 */
class Redirect extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|Redirector
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
        return 'redirect';
    }
}