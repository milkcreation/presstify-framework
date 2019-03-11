<?php

namespace tiFy\Contracts\Wp;

use tiFy\Contracts\Support\Collection;
use WP_Term_Query;

interface QueryTerms extends Collection
{
    /**
     * Récupération d'une instance basée sur une liste d'arguments.
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/
     *
     * @param array $args Liste des arguments de la requête récupération des éléments
     *
     * @return static
     */
    public static function createFromArgs($args = []);

    /**
     * Récupération d'une instance basée sur la liste d'identifiant de qualification de termes.
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/
     *
     * @param array $ids Liste des identifiants de qualification.
     *
     * @return static
     */
    public static function createFromIds(array $ids);

    /**
     * Récupération de la liste des identifiants de qualification.
     *
     * @return array
     */
    public function getIds();

    /**
     * Récupération de la liste des identifiants de qualification.
     *
     * @return array
     */
    public function getNames();

    /**
     * Récupération de la liste des identifiants de qualification.
     *
     * @return array
     */
    public function getSlugs();

    /**
     * Récupération de l'instance de la requête Wordpress de récupération des termes.
     *
     * @return null|WP_Term_Query
     */
    public function WpTermQuery() : WP_Term_Query;
}