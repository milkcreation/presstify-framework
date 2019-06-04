<?php declare(strict_types=1);

namespace tiFy\Validation;

use tiFy\Container\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'validator'
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->add('validator', function () {
            return new Validator();
        });
    }
}