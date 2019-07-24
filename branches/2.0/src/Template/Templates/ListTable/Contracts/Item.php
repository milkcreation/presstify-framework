<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Template\FactoryAwareTrait;

interface Item extends FactoryAwareTrait, ParamsBag
{
    /**
     * Délégation d'appel des méthodes du de l'object associé.
     *
     * @param string $name Nom de qualification de la méthode.
     * @param array $args Liste des paramètres passés en arguments à la méthode.
     *
     * @return mixed
     */
    public function __call($name, $args);

    /**
     * Récupération de la valeur de l'attribut de qualification de l'élément.
     *
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getKeyValue($default = null);

    /**
     * Récupération de la clé d'indice de l'attribut de qualification de l'élément.
     *
     * @return string
     */
    public function getKeyName(): string;

    /**
     * Récupération de l'indice de l'élément.
     *
     * @return int
     */
    public function getIndex(): int;

    /**
     * @inheritDoc
     */
    public function parse(): Item;

    /**
     * Traitement de l'object de délégation d'appel des méthodes de la classe.
     *
     * @return static
     */
    public function parseDelegate(): Item;

    /**
     * Définition de l'indice de l'élément.
     *
     * @param int $index
     *
     * @return static
     */
    public function setIndex(int $index): Item;
}