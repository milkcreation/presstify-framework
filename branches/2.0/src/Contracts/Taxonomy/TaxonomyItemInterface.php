<?php

namespace tiFy\Contracts\Taxonomy;

use tiFy\Contracts\Kernel\ParamsBag;
use WP_Taxonomy;

interface TaxonomyItemInterface extends ParamsBag
{
    /**
     * Récupération du nom de qualification du type de post.
     *
     * @return string
     */
    public function getName();

    /**
     * Déclaration du type de post.
     *
     * @return WP_Taxonomy
     */
    public function register();
}