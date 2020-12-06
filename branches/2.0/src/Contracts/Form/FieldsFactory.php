<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use ArrayAccess, Countable, IteratorAggregate;
use Illuminate\Support\Collection;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 */
interface FieldsFactory extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Récupération de la liste des pilotes déclarés.
     *
     * @return FieldDriver[]|array
     */
    public function all(): array;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): FieldsFactory;

    /**
     * Collection.
     *
     * @param array|null $items Si null, liste des pilotes déclarés
     *
     * @return Collection|FieldDriver[]|iterable
     */
    public function collect(?array $items = null): iterable;

    /**
     * Récupération d'un pilote déclaré selon son alias.
     *
     * @param string $alias
     *
     * @return FieldDriver|null
     */
    public function get(string $alias): ?FieldDriver;

    /**
     * Récupération de la liste des champs par groupe d'appartenance.
     *
     * @param string $groupAlias Alias de qualification du groupe.
     *
     * @return Collection|FieldDriver[]|null
     */
    public function fromGroup(string $groupAlias): ?iterable;

    /**
     * Récupération de valeur(s) de champ(s) basée(s) sur leurs variables d'identifiant de qualification.
     *
     * @param mixed $tags Variables de qualification de champs.
     * string ex. "%%{{slug#1}}%% %%{{slug#2}}%%"
     * array ex ["%%{{slug#1}}%%", "%%{{slug#2}}%%"]
     * @param boolean $raw Activation de la valeur de retour au format brut.
     *
     * @return string|null
     */
    public function metatagsValue($tags, bool $raw = true): ?string;

    /**
     * Pré-traitement de la liste des champs en vue d'un affichage du rendu.
     *
     * @return FieldsFactory
     */
    public function preRender(): FieldsFactory;
}