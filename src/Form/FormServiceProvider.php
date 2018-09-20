<?php

namespace tiFy\Form;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Form\Addons\AddonsController;
use tiFy\Form\Buttons\ButtonsController;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Form;


class FormServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var string[]
     */
    protected $bindings = [];

    /**
     * Liste des services à instance unique auto-déclarés.
     * @var string[]
     */
    protected $singletons = [
        AddonsController::class,
        ButtonsController::class,
        FieldTypesController::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(Form::class, function () {
            return new Form();
        })->build();
    }
}