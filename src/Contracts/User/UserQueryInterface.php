<?php

namespace tiFy\Contracts\User;

interface UserQueryInterface
{
    /**
     * Récupération des données d'une liste d'élément selon des critères de requête
     *
     * @param array $query_args Liste des arguments de requête
     *
     * @return array|UserQueryCollectionInterface|UserQueryItemInterface[]
     */
    public function getCollection($query_args = []);

    /**
     * Récupération d'un élément
     *
     * @param string|int|\WP_User|null $id Login utilisateur Wordpress|Identifiant de qualification Wordpress|Objet utilisateur Wordpress|Utilisateur Wordpress courant
     *
     * @return null|UserQueryItemInterface
     */
    public function getItem($id = null);

    /**
     * Récupération d'un élément selon un attribut particulier
     *
     * @param string $key Identifiant de qualification de l'attribut. défaut name.
     * @param string $value Valeur de l'attribut
     *
     * @return null|UserQueryItemInterface
     */
    public function getItemBy($key = 'login', $value);

    /**
     * Récupération du(es) role(s) utilisateur Wordpress du controleur.
     *
     * @return string|array
     */
    public function getObjectName();


    /**
     * Récupération d'une instance du controleur de liste d'éléments.
     *
     * @param UserItemInterface[] $items Liste des éléments.
     *
     * @return string
     */
    public function resolveCollection($items);

    /**
     * Récupération d'une instance du controleur de données d'un élément.
     *
     * @param \WP_User $wp_user Instance d'utilisateur Wordpress.
     *
     * @return string
     */
    public function resolveItem(\WP_User $wp_user);
}

