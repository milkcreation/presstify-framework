<?php

declare(strict_types=1);

namespace tiFy\Container\Services;

use Pollen\Form\FormManager;
use Pollen\Form\FormManagerInterface;
use tiFy\Container\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        FormManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(FormManagerInterface::class, function () {
            return new FormManager([], $this->getContainer());
        });
    }
}