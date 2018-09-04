<?php

namespace tiFy\Contracts\Taxonomy;

interface TermQueryInterface
{
    /**
     * Récupération d'une liste d'élément selon des critères de requête
     *
     * @param array $query_args Liste des arguments de requête
     *
     * @return array|TermItemInterface[]
     */
    public function getCollection($query_args = []);

    /**
     * Récupération du controleur de données d'une liste d'éléments
     *
     * @return string
     */
    public function getCollectionController();

    /**
     * Récupération d'un élément
     *
     * @param string|int|\WP_Term|null $id Nom de qualification (slug)|Identifiant de term Wordpress|Objet terme Wordpress|Terme de ma page courante
     *
     * @return null|object|TermItemInterface
     */
    public function getItem($id = null);

    /**
     * Récupération d'un élément selon un attribut particulier
     *
     * @param string $key Identifiant de qualification de l'attribut. défaut name.
     * @param string $value Valeur de l'attribut
     *
     * @return null|object|TermItemInterface
     */
    public function getItemBy($key = 'slug', $value);

    /**
     * Récupération du controleur de données d'un élément
     *
     * @return string
     */
    public function getItemController();

    /**
     * Récupération de la taxonomie Wordpress du controleur
     *
     * @return string
     */
    public function getObjectName();
}