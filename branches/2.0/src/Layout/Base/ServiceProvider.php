<?php

namespace tiFy\Layout\Base;

use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Kernel\Container\ServiceProvider as KernelServiceProvider;

class ServiceProvider extends KernelServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->getContainer()->singleton('layout.db', DbBaseController::class);

        $this->getContainer()->singleton('layout.labels', LabelsBaseController::class);

        $this->getContainer()->singleton('layout.params', ParamsBaseController::class);

        $this->getContainer()->singleton('layout.notices', NoticesBaseController::class);

        $this->getContainer()->singleton('layout.request', RequestBaseController::class);
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