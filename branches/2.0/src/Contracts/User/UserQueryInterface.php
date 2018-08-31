<?php

namespace tiFy\Contracts\User;

use tiFy\Contracts\User\UserQueryCollectionInterface;
use tiFy\Contracts\User\UserQueryItemInterface;

interface UserQueryInterface
{
    /**
     * Récupération des données d'une liste d'élément selon des critères de requête
     *
     * @param array $query_args Liste des arguments de requête
     *
     * @return array|UserQueryItemInterface[]|UserQueryCollectionInterface
     */
    public function getCollection($query_args = []);

    /**
     * Récupération du controleur de données d'une liste d'éléments.
     *
     * @return string
     */
    public function getCollectionController();

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
     * Récupération du controleur de données d'un élément.
     *
     * @return string
     */
    public function getItemController();

    /**
     * Récupération du(es) role(s) utilisateur Wordpress du controleur.
     *
     * @return string|array
     */
    public function getObjectName();
}

