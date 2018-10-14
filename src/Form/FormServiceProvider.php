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
     * {@inheritdoc}
     */
    public function boot()
    {
        $singletons = [
            Form::class,
            AddonsController::class,
            ButtonsController::class,
            FieldTypesController::class
        ];

        foreach($singletons as $singleton) :
            $this->app->singleton($singleton)->build();
        endforeach;
    }
}