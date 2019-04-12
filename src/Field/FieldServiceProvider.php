<?php

namespace tiFy\Field;

use tiFy\Container\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'field',
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('field', function () {
            return new FieldManager();
        });
    }
}