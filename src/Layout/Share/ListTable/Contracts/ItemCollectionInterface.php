<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use ArrayIterator;
use ArrayAccess;
use Countable;
use IteratorAggregate;

interface ItemCollectionInterface extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Récupération de la liste des éléments.
     *
     * @return array
     */
    public function all();

    /**
     * Retourne le nombre d'éléments trouvés.
     *
     * @return int
     */
    public function count();

    /**
     * Récupération de l'itérateur.
     *
     * @return ArrayIterator
     */
    public function getIterator();

    /**
     * Récupération du nombre total d'éléments.
     *
     * @return int
     */
    public function getTotal();

    /**
     * Vérification d'existance d'éléments
     *
     * @return bool
     */
    public function has();

    /**
     * Traitement de récupération de la liste des éléments.
     *
     * @param array $query_args Liste des arguments de requête de récupération
     *
     * @return void
     */
    public function query($query_args = []);
}