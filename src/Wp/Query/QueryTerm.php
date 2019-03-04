<?php

namespace tiFy\Wp\Query;

use tiFy\Taxonomy\Query\TermQueryItem;
use WP_Term;

class QueryTerm extends TermQueryItem
{
    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Term $wp_term Objet terme Wordpress.
     *
     * @return void
     */
    public function __construct(WP_Term $wp_term)
    {
        parent::__construct($wp_term);
    }
}