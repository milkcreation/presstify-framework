<?php

namespace tiFy\Contracts\Wp;

interface WpResolverTrait
{
    /**
     * Récupération de l'instance du controleur des taxonomies.
     *
     * @return WpTaxonomyManager
     */
    public function taxonomy();

    /**
     * Récupération de l'instance du controleur des utilisateurs.
     *
     * @return User
     */
    public function user();
}