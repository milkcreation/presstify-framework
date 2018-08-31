<?php

namespace tiFy\Query\Controller;

interface QueryInterface
{
    /**
     * Récupération du(es) type(s) de post Wordpress du controleur.
     *
     * @return string|array
     */
    public function getObjectName();

    /**
     * Récupération du controleur de données d'un élément.
     *
     * @return string
     */
    public function getItemController();

    /**
     * Récupération du controleur de données d'une liste d'éléments.
     *
     * @return string
     */
    public function getListController();

    /**
     * Récupération d'un élément.
     *
     * @param string|int|object|null $id Nom de qualification de l'objet|Identifiant de qualification de l'objet|Instance de l'objet|Objet affiché sur la page courante.
     *
     * @return null|object
     */
    public function get($id = null);

    /**
     * Récupération d'un élément selon un attribut particulier.
     *
     * @param string $key Identifiant de qualification de l'attribut. défaut name.
     * @param string $value Valeur de l'attribut.
     *
     * @return null|object
     */
    public function getBy($key = 'name', $value);

    /**
     * Récupération des données d'une liste d'élément selon des critères de requête.
     *
     * @param array $query_args Liste des arguments de requête.
     *
     * @return array
     */
    public function getList($query_args = []);
}