<?php

namespace tiFy\App\Container;

use Illuminate\Support\Collection;
use tiFy\App\AppController;
use tiFy\App\AppInterface;
use tiFy\App\AppTrait;
use tiFy\Kernel\Container\Container as KernelContainer;
use tiFy\Kernel\Container\ContainerInterface;
use tiFy\Kernel\Container\ContainerTrait;
use tiFy\Kernel\Container\ServiceInterface;
use tiFy\Kernel\Container\ServiceProviderInterface;

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
}