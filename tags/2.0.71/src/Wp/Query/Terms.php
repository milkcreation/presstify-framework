<?php

namespace tiFy\Wp\Query;

use tiFy\Taxonomy\Query\TermQueryCollection;
use WP_Term;
use WP_Term_Query;

class Terms extends TermQueryCollection
{
    /**
     * CONSTRUCTEUR.
     *
     * @param null|WP_Term_Query $wp_term_query Requête Wordpress de récupération de termes.
     *
     * @return void
     */
    public function __construct(WP_Term_Query $wp_term_query)
    {
        parent::__construct($wp_term_query instanceof WP_Term_Query ? $wp_term_query->terms : []);
    }

    /**
     * Récupération d'une instance basée sur la liste des arguments.
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/
     *
     * @param array $args Liste des arguments de la requête récupération des éléments
     *
     * @return static
     */
    public static function createFromArgs($args = [])
    {
        return new static(new WP_Term_Query($args));
    }

    /**
     * {@inheritdoc}
     *
     * @param WP_Term $item Objet terme Wordpress.
     *
     * @return void
     */
    public function wrap($item, $key = null)
    {
        $this->items[$key] = app()->get('wp.query.term', [$item]);
    }
}