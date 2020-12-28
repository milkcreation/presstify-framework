<?php declare(strict_types=1);

namespace tiFy\Form;

use InvalidArgumentException;
use tiFy\Contracts\Form\FieldDriver;

class FieldValidateException extends InvalidArgumentException
{
    /**
     * Alias de qualification.
     * @var string
     */
    private $alias = '';

    /**
     * Instance du champ associé.
     * @var FieldDriver|null
     */
    private $field;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Récupération de l'instance du pilote de champ.
     *
     * @return FieldDriver
     */
    public function getField(): FieldDriver
    {
        return $this->field;
    }

    /**
     * Vérification de correspondance de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return bool
     */
    public function is(string $alias): bool
    {
        return $this->alias === $alias;
    }

    /**
     * Vérification de correspondance de l'alias de qualification.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->alias === '_required';
    }

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Définition de l'instance du champ associé.
     *
     * @param FieldDriver $field
     *
     * @return static
     */
    public function setField(FieldDriver $field): self
    {
        $this->field = $field;

        return $this;
    }
}