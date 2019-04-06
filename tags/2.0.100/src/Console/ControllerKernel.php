<?php

namespace tiFy\Console;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as SfKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ControllerKernel extends SfKernel
{
    public function __construct(string $environment, bool $debug)
    {
        container()->share('event_dispatcher', new EventDispatcher());

        parent::__construct($environment, $debug);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return WP_CONTENT_DIR . '/uploads/var/cache/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return WP_CONTENT_DIR . '/uploads/var/log';
    }

    /**
     * @inheritdoc
     */
    public function registerBundles()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function getContainer()
    {
        return container();
    }
}