<?php

namespace tiFy\TabMetabox;

use tiFy\TabMetabox\Controller\ContentController;

class ContentTaxonomyController extends ContentController
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
    public function display($term, $taxonomy, $args = [])
    {
        return $this->appTemplateRender('display', $this->all());
    }
}