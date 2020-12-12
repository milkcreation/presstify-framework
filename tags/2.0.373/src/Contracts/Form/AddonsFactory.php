<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use ArrayAccess, Countable, IteratorAggregate;
use Illuminate\Support\Collection;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 */
interface AddonsFactory extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Récupération de la liste des pilotes déclarés.
     *
     * @return AddonDriver[]|array
     */
    public function all(): array;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): AddonsFactory;

    /**
     * Collection.
     *
     * @param array|null $items Si null, liste des pilotes déclarés
     *
     * @return Collection|AddonDriver[]|iterable
     */
    public function collect(?array $items = null): iterable;

    /**
     * Récupération d'un pilote déclaré selon son alias.
     *
     * @param string $alias
     *
     * @return AddonDriver|null
     */
    public function get(string $alias): ?AddonDriver;
}