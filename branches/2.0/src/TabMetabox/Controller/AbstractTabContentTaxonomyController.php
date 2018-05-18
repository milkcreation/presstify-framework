<?php

namespace tiFy\TabMetabox\Controller;

abstract class AbstractTabContentTaxonomyController extends AbstractTabContentController
{
    /**
     * Affichage.
     *
     * @param \WP_Term $term Objet du terme courant Wordpress.
     * @param string $taxonomy Nom de de qualification de la taxonomie associée au terme.
     * @param array $args Liste des vaiables passés en argument.
     *
     * @return string
     */
    public function display($term, $taxonomy, $args)
    {
        parent::display();
    }
}