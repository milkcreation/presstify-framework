<?php declare(strict_types=1);

namespace tiFy\Validation;

use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Validator as BaseValidator;
use tiFy\Contracts\Validation\Rule;
use tiFy\Contracts\Validation\Validator as ValidatorContract;
use tiFy\Validation\Rules\Password;

/**
 * @method static Password password(array $args = [])
 */
class Validator extends BaseValidator implements ValidatorContract
{
    /**
     * Liste des régles personnalisées.
     * @var Rule[]
     */
    protected static $customs = [];

    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container|null
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param Container|null $container Instance du conteneur d'injection de dépendances.
     * @param array $rules Liste des régles personnalisées.
     *
     * @return void
     */
    public function __construct(?Container $container = null, array $rules = [])
    {
        $this->container = $container;

        array_walk($rules, function ($rule, $key) {
            if ($rule instanceof Rule) {
                self::$customs[$key] = $rule->setValidator($this)->setName($key);
            }
        });

        parent::__construct($rules);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ComponentException
     */
    public static function buildRule($ruleSpec, $arguments = [])
    {
        if (is_string($ruleSpec) && isset(self::$customs[$ruleSpec])) {
            return self::buildRule(self::$customs[$ruleSpec]->setArgs(...$arguments), $arguments);
        } else {
            return parent::buildRule($ruleSpec, $arguments);
        }
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }
}