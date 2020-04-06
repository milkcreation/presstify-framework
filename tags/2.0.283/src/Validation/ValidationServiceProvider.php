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
        'validator.rule.password',
        'validator.rule.serialized',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('validator', function () {
            $rules = [
                // @todo Surchage de \Respect\Validation\Factory pour permettre la surchage  des règles existantes
                // @see \Respect\Validation\Factory::$rulesNamespaces
                'password'   => $this->getContainer()->get('validator.rule.password'),
                'serialized' => $this->getContainer()->get('validator.rule.serialized'),
            ];

            foreach ($rules as $name => $rule) {
                Validator::setCustom($name, $rule);
            }

            return new Validator();
        });

        $this->getContainer()->add('validator.rule.password', function () {
            return new Rules\Password();
        });

        $this->getContainer()->add('validator.rule.serialized', function () {
            return new Rules\Serialized();
        });
    }
}