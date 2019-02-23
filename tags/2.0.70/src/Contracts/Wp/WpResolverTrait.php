<?php

namespace tiFy\Contracts\Wp;

interface WpResolverTrait
{
    /**
     * Récupération de l'instance du controleur des type de post.
     *
     * @return PostType
     */
    public function post_type();

    /**
     * Récupération de l'instance du controleur des taxonomies.
     *
     * @return Taxonomy
     */
    public function taxonomy();

    /**
     * Récupération de l'instance du controleur des utilisateurs.
     *
     * @return User
     */
    public function user();
}