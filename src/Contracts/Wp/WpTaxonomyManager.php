<?php

namespace tiFy\Contracts\Wp;

use WP_Term;

interface WpTaxonomyManager
{
    /**
     * Récupération de la liste des termes selon l'ordre.
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/
     *
     * @param string|array $taxonomy Nom ou liste de taxonomies associées.
     * @param array $attrs Liste des arguments de récupération des éléments.
     * @param string $order_meta_key Clé d'indice de la metadonnée de traitement de l'ordre
     *
     * @return WP_Term[]|int
     */
    public function getTermsByOrder($taxonomy, $args = [], $order_meta_key = '_order');
}