<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use ArrayAccess, Countable, IteratorAggregate;
use Illuminate\Support\Collection;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 */
interface FieldGroupsFactory extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Récupération de la liste des pilotes déclarés.
     *
     * @return FieldGroupDriver[]|array
     */
    public function all(): array;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): FieldGroupsFactory;

    /**
     * Collection.
     *
     * @param array|null $items Si null, liste des pilotes déclarés.
     *
     * @return Collection|FieldGroupsFactory[]|iterable
     */
    public function collect(?array $items = null): iterable;

    /**
     * Récupération d'un pilote déclaré selon son alias.
     *
     * @param string $alias
     *
     * @return FieldGroupDriver|null
     */
    public function get(string $alias): ?FieldGroupDriver;

    /**
     * Récupération de l'indice incrémentale de qualification d'un groupe.
     *
     * @return int
     */
    public function getIncrement(): int;

    /**
     * Définition d'un pilote.
     *
     * @param string $alias
     * @param array|FieldGroupDriver $driverDefinition
     *
     * @return static
     */
    public function setDriver(string $alias, $driverDefinition = []): FieldGroupsFactory;
}