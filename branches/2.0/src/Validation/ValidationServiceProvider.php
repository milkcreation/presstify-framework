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
        'validator',
        'validator.rule.password'
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->getContainer()->share('validator', function () {
            return new Validator($this->getContainer(), [
                'password' => $this->getContainer()->get('validator.rule.password')
            ]);
        });

        $this->getContainer()->add('validator.rule.password', function () {
            return new Rules\Password();
        });
    }
}