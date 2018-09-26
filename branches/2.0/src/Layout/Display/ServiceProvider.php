<?php

namespace tiFy\Layout\Display;

use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Kernel\Container\ServiceProvider as KernelServiceProvider;

class ServiceProvider extends KernelServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->getContainer()->singleton('db', DbBaseController::class);

        $this->getContainer()->singleton('labels', LabelsBaseController::class);

        $this->getContainer()->singleton('params', ParamsBaseController::class);

        $this->getContainer()->singleton('notices', NoticesBaseController::class);

        $this->getContainer()->singleton('request', RequestBaseController::class);
    }

    /**
     * Récupération de la disposition associée.
     *
     * @return LayoutDisplayInterface
     */
    public function layout()
    {
        return $this->getContainer();
    }
}