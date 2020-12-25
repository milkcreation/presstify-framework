<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use ArrayAccess, Countable, IteratorAggregate;
use Illuminate\Support\Collection;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 */
interface ButtonsFactory extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Récupération de la liste des pilotes déclarés.
     *
     * @return ButtonDriver[]|array
     */
    public function all(): array;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): ButtonsFactory;

    /**
     * Collection.
     *
     * @param array|null $items Si null, liste des pilotes déclarés.
     *
     * @return Collection|ButtonDriver[]|iterable
     */
    public function collect(?array $items = null): iterable;

    /**
     * Récupération de la liste des éléments par ordre d'affichage.
     *
     * @return Collection|ButtonDriver[]|iterable
     */
    public function collectByPosition(): iterable;

    /**
     * Récupération d'un pilote déclaré selon son alias.
     *
     * @param string $alias
     *
     * @return ButtonDriver|null
     */
    public function get(string $alias): ?ButtonDriver;
}