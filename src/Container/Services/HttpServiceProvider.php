<?php

declare(strict_types=1);

namespace tiFy\Container\Services;

use Pollen\Http\Request;
use Pollen\Http\RequestInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequestInterface;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class HttpServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        PsrRequestInterface::class,
        RequestInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(RequestInterface::class, function () {
            return Request::getFromGlobals();
        });

        $this->getContainer()->share(PsrRequestInterface::class, function () {
            return Request::createPsr();
        });
    }
}