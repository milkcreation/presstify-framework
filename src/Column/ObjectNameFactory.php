<?php

namespace tiFy\Column;

use tiFy\Apps\AppController;

final class objectNameFactory extends AppController
{
    /**
     * Type d'objet
     * @var string
     */
    protected $objectType = '';

    /**
     * Identifiant de qualification du type d'objet
     * @var string
     */
    protected $objectName = '';

    /**
     * Liste des colonnes à ajouter
     * @var array
     */
    protected $addColumn = [];

    /**
     * Liste des colonnes à supprimer
     * @var array
     */
    protected $removeColumn = [];

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct($object_type, $object_name)
    {
        // Définition des variables d'environnement
        $this->objectType = $object_type;
        $this->objectName = $object_name;

        // Déclaration des événements de déclenchement
        $this->appAddAction('admin_init', null, 99);
    }

    /**
     * Initialisation de l'interface d'administration
     *
     * @return void
     */
    public function admin_init()
    {
        // Ajout des colonnes personnalisé
        if ($this->addColumn) :
            foreach ($this->addColumn as $name => $controller) :
                $this->_add($name, $controller);
            endforeach;
        endif;

        switch ($this->objectType) :
            case 'post_type' :
                $this->appAddFilter('manage_edit-' . $this->objectName . '_columns', '_remove', 50, 2);
                break;

            case 'taxonomy' :
                $this->appAddFilter('manage_edit-' . $this->objectName  . '_columns', '_remove', 50, 3);
                break;

            case 'custom' :
                $this->appAddFilter('manage_' . $this->objectName . '_columns', '_remove', 50, 3);
                break;
        endswitch;
    }

    /**
     * Instanciation des controleurs d'affichage de colonne.
     *
     * @param string $name Identifiant de qualification du controleur d'affichage.
     * @param array|ColumnFactory $controller {
     *      Liste des attributs de configuration|Classe de rappel.
     *
     *      @var string $id Identifiant de qualification.
     *      @var string $object_type Type d'objet.
     *      @var string $object_name Identifiant de qualification du type d'objet.
     *      @var string $column_name Identifiant de qualification de la colonne.
     *      @var string $title Intitulé de la colonne.
     *      @var int $position Position de la colonne dans la table.
     *      @var string|callable Affichage du rendu de la cellule.
     * }
     *
     * @return null|ColumnFactory
     */
    private function _add($name, $controller)
    {
        if (is_array($controller)) :
            $args = $controller;
            $controller = 'tiFy\Column\ColumnPostType';
        elseif (is_object($controller) || class_exists($controller)) :
            $args = [];
        else :
            return null;
        endif;

        // Instanciation
        if (!is_object($controller)) :
            $call = new $controller($args);
        else :
            $call = $controller;
        endif;

        $call($name, $this->objectType, $this->objectName);
    }

    /**
     * Suppression des colonnes.
     *
     * @param string $name Identifiant de qualification du controleur d'affichage
     * @param array|ColumnFactory $controller {
     *      Liste des attributs de configuration|Classe de rappel
     *
     *      @var string $id Identifiant de qualification
     *      @var string $object_type Type d'objet
     *      @var string $object_name Identifiant de qualification du type d'objet
     *      @var string $column_name Identifiant de qualification de la colonne
     *      @var string $title Intitulé de la colonne
     *      @var int $position Position de la colonne dans la table
     *      @var string|callable Affichage du rendu de la cellule
     * }
     *
     * @return void
     */
    final public function _remove($columns)
    {
        if($removes = array_intersect($this->removeColumn, array_keys($columns))) :
            foreach($removes as $name) :
                unset($columns[$name]);
            endforeach;
        endif;

        return $columns;
    }

    /**
     * Ajout d'une colonne.
     *
     * @param string $name Identifiant de qualification du controleur d'affichage
     * @param string|array|ColumnFactory $controller {
     *      Liste des attributs de configuration|Classe de rappel
     *
     *      @var string $column_name Identifiant de qualification de la colonne
     *      @var string $title Intitulé de la colonne
     *      @var int $position Position de la colonne dans la table
     *      @var string|callable $content Affichage du rendu de la cellule
     * }
     *
     * @return $this
     */
    public function add($name, $controller)
    {
        if (did_action('tify_custom_columns_register_too_late')) :
            wp_die(
                __('La déclaration des colonnes à ajouter doit se faire avant l\'exécution de l\'action "tify_custom_columns_register"'),
                'Déclaration en échec',
                500
            );
            exit;
        endif;

        $this->addColumn[$name] = $controller;

        return $this;
    }

    /**
     * Suppression d'un colonne.
     *
     * @param string $name Identifiant de qualification de la colonne à supprimer.
     *
     * @return $this
     */
    public function remove($name)
    {
        if (did_action('tify_custom_columns_register_too_late')) :
            wp_die(
                __('La déclaration des colonnes à supprimer doit se faire avant l\'exécution de l\'action "tify_custom_columns_register"'),
                'Déclaration en échec',
                500
            );
            exit;
        endif;

        array_push($this->removeColumn, $name);

        return $this;
    }
}