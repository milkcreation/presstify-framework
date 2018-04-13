<?php
namespace tiFy\Core\Taboox\Taxonomy;

abstract class Admin extends \tiFy\Core\Taboox\Admin
{
    /**
     * Formulaire de saisie.
     *
     * @param \WP_Term $term Objet du terme courant Wordpress.
     * @param string $taxonomy Identifiant de qualification de la taxonomie associée au terme.
     *
     * @return void
     */
    public function form($term, $taxonomy)
    {

    }
}