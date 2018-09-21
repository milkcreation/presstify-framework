<?php

namespace tiFy\App\Container;

use Illuminate\Support\Collection;
use tiFy\App\AppController;
use tiFy\App\AppTrait;
use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Contracts\Container\ServiceInterface;
use tiFy\Contracts\Container\ServiceProviderInterface;
use tiFy\Kernel\Container\Container as KernelContainer;
use tiFy\Kernel\Container\ContainerTrait;

class AppContainer extends KernelContainer implements AppInterface
{
    use AppTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->appAddAction('tify_app_boot', [$this, 'appBoot']);
    }

    /**
     * {@inheritdoc}
     */
    public function appBoot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function addService($abstract, $attrs = [])
    {
        return new AppService($abstract, $attrs, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceProviders()
    {
        return array_merge(
            $this->serviceProviders,
            $this->appConfig('providers', [])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function has($alias)
    {
        return container()->has($alias);
    }
}