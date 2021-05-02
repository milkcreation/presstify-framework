<?php

declare(strict_types=1);

namespace tiFy\Form\Factory;

use Exception;
use LogicException;
use Pollen\Validation\Validator as v;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Contracts\Form\ValidateFactory as ValidateFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;

class ValidateFactory implements ValidateFactoryContract
{
    use FormAwareTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Cartographie des alias de fonction de contrôle d'intégrité
     * @var array
     */
    protected $aliases = [];

    /**
     * @inheritDoc
     */
    public function boot(): ValidateFactoryContract
    {
        if ($this->booted === false) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $this->form()->event('validate.boot', [&$this]);

            $this->booted = true;

            $this->form()->event('validate.booted', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function call($callback, $value, $args = []): bool
    {
        $_args = $args;
        array_unshift($args, $value);

        if (is_string($callback)) {
            try {
                if (preg_match('/^!(.*)/', $callback, $match)) {
                    $callback = $match[1];

                    return !empty($_args)
                        ? !v::$callback(...$_args)->validate($value) : !v::$callback()->validate($value);
                } else {
                    return !empty($_args)
                        ? v::$callback(...$_args)->validate($value) : v::$callback()->validate($value);
                }
            } catch (Exception $e) {
                if (is_callable([$this, $callback])) {
                    return $this->{$callback}(...$args);
                } elseif (function_exists($callback)) {
                    return $callback(...$args);
                }
            }
        } elseif (is_callable($callback)) {
            return $callback(...$args);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function __return_true($value): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function compare($value, $tags, $raw = true): bool
    {
        return v::equals($this->form()->fieldTagsValue($tags, $raw))->validate($value);
    }
}