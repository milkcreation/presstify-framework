<?php

namespace tiFy\Contracts\Taxonomy;

interface TermQueryInterface
{
    /**
     * Récupération d'une liste d'élément selon des critères de requête
     *
     * @param array $query_args Liste des arguments de requête
     *
     * @return array|TermQueryCollectionInterface|TermQueryItemInterface[]
     */
    public function getCollection($query_args = []);

    /**
     * Récupération d'un élément
     *
     * @param string|int|\WP_Term|null $id Nom de qualification (slug)|Identifiant de term Wordpress|Objet terme Wordpress|Terme de ma page courante
     *
     * @return null|object|TermQueryItemInterface
     */
    public function getItem($id = null);

    /**
     * Récupération d'un élément selon un attribut particulier
     *
     * @param string $key Identifiant de qualification de l'attribut. défaut name.
     * @param string $value Valeur de l'attribut
     *
     * @return null|object|TermQueryItemInterface
     */
    public function getItemBy($key = 'slug', $value);

    /**
     * Récupération de la taxonomie Wordpress du controleur
     *
     * @return string
     */
    public function getObjectName();

    /**
     * Récupération d'une instance du controleur de liste d'éléments.
     *
     * @param TermQueryItemInterface[] $items Liste des éléments.
     *
     * @return string
     */
    public function resolveCollection($items);

    /**
     * Récupération d'une instance du controleur de données d'un élément.
     *
     * @param \WP_Term $wp_term Instance de terme de taxonomie Wordpress.
     *
     * @return string
     */
    public function resolveItem(\WP_Term $wp_term);
}