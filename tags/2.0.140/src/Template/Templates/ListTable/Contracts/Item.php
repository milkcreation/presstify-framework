<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Template\{FactoryAwareTrait, FactoryDb};

interface Item extends FactoryAwareTrait, ParamsBag
{
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
     * Récupération de l'instance du modèle.
     * {@internal Le controleur de base de données doit être actif.}
     *
     * @return FactoryDb|null
     */
    public function model(): ?FactoryDb;

    /**
     * Définition de l'instance de l'objet associé à l'élément.
     *
     * @param object $object
     *
     * @return static
     */
    public function setObject(object $object): Item;
}