<?php

namespace tiFy\Contracts\PostType;

interface PostQueryInterface
{
    /**
     * Récupération des données d'une liste d'élément selon des critères de requête.
     *
     * @param array $query_args Liste des arguments de requête.
     *
     * @return array|PostQueryCollectionInterface|PostQueryItemInterface[]
     */
    public function getCollection($query_args = []);

    /**
     * Récupération d'un élément.
     *
     * @param string|int|\WP_Post|null $id Nom de qualification du post WP (slug, post_name)|Identifiant de qualification du post WP|Object post WP|Post WP  de la page courante
     *
     * @return null|PostQueryItemInterface
     */
    public function getItem($id = null);

    /**
     * Récupération d'un élément selon un attribut particulier.
     *
     * @param string $key Identifiant de qualification de l'attribut. défaut name.
     * @param string $value Valeur de l'attribut.
     *
     * @return null|PostQueryItemInterface
     */
    public function getItemBy($key = 'name', $value);

    /**
     * Récupération du(es) type(s) de post Wordpress du controleur.
     *
     * @return string|array
     */
    public function getObjectName();

    /**
     * Récupération d'une instance du controleur de liste d'éléments.
     *
     * @param PostQueryItemInterface[] $items Liste des éléments.
     *
     * @return string
     */
    public function resolveCollection($items);

    /**
     * Récupération d'une instance du controleur de données d'un élément.
     *
     * @param \WP_Post $wp_post Instance de post Wordpress.
     *
     * @return string
     */
    public function resolveItem(\WP_Post $wp_post);
}

