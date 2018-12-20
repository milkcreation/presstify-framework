<?php

namespace tiFy\Wp;

class WpTaxonomy
{
    /**
     * Récupération de la liste des termes selon l'ordre.
     * @see https://developer.wordpress.org/reference/functions/get_terms/
     *
     * @param string|array $taxonomy Nom ou liste de taxonomies associées.
     * @param array $attrs Liste des attributs de récupération des éléments.
     *
     * @return \WP_Term[]
     */
    public function getTermsByOrder($taxonomy, $attrs = [])
    {
        return get_terms(
            wp_parse_args(
                [
                    'taxonomy'   => $taxonomy,
                    'meta_query' => [
                        [
                            'relation' => 'OR',
                            [
                                'key'     => '_order',
                                'value'   => 0,
                                'compare' => '>=',
                                'type'    => 'NUMERIC',
                            ],
                            [
                                'key'     => '_order',
                                'compare' => 'NOT EXISTS',
                            ],
                        ],
                    ],
                    'orderby'    => 'meta_value_num',
                    'order'      => 'ASC',
                ],
                $attrs
            )
        );
    }
}