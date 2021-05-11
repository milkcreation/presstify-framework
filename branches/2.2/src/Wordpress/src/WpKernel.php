<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use App\App;
use Pollen\Kernel\ApplicationInterface;
use Pollen\Kernel\Kernel;
use RuntimeException;

class WpKernel extends Kernel
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (!$this->isBooted()) {
            if (defined('WP_INSTALLING') && (WP_INSTALLING === true)) {
                return;
            }

            parent::boot();
        }
    }

    /**
     * Chargement de l'application.
     *
     * @return void
     */
    protected function bootApp(): void
    {
        $this->app = class_exists(App::class) ? new App() : new WpApplication();

        if (!$this->app instanceof WpApplicationInterface) {
            throw new RuntimeException(sprintf('Application must be an instance of %s', WpApplicationInterface::class));
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return WpApplicationInterface
     */
    public function getApp(): ApplicationInterface
    {
        return parent::getApp();
    }
}
