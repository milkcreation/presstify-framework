<?php

namespace tiFy\Contracts\Metabox;

interface MetaboxDisplayTermInterface extends MetaboxDisplayInterface
{
    /**
     * Affichage.
     *
     * @param \WP_Term $term Objet du terme courant Wordpress.
     * @param string $taxonomy Nom de de qualification de la taxonomie associée au terme.
     * @param array $args Liste des variables passés en argument.
     *
     * @return string
     */
    public function display($term, $taxonomy, $args = []);
}