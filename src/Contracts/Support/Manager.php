<?php declare(strict_types=1);

namespace tiFy\Contracts\Support;

interface Manager
{
    /**
     * Récupération d'un élément définit.
     *
     * @param string|int $key Indice de qualification de l'élément.
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Déclaration d'un élément basée sur une liste d'attributs.
     *
     * @param string|int $key Indice de qualification de l'élément.
     * @param array $attrs Liste des attributs.
     *
     * @return static
     */
    public function register($key, array $attrs = []);

    /**
     * Définition d'un élément ou d'une liste d'éléments.
     *
     * @param string|int|array $key Indice de qualification de l'élément ou tableau associatif de la liste des éléments.
     * @param mixed $item Valeur de l'élément lorsque la clef est un indice.
     *
     * @return static
     */
    public function set($key, $item = null);

    /**
     * Traitement des éléments au moment de la définition.
     *
     * @param mixed $item Elément à définir
     * @param string|null Indice de qualification de l'élément.
     *
     * @return void
     */
    public function walk(&$item, $key = null): void;
}