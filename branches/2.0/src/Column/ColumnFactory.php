<?php

namespace tiFy\Column;

use tiFy\Apps\AppController;

abstract class ColumnFactory extends AppController
{
    /**
     * Identifiant de qualification de la classe de rappel
     * @var string
     */
    protected $id = '';

    /**
     * Type d'objet
     * @var string post_type|taxonomy|custom
     */
    protected $objectType = '';

    /**
     * Identifiant de qualification du type d'objet
     * @var string post|page|{custom_type}|category|tag|{custom_taxonomy}|{custom_name}
     */
    protected $objectName = '';

    /**
     * Identifiant de qualification de la colonne
     * @var string
     */
    protected $columnName = '';

    /**
     * Intitulé de la colonne
     * @var string
     */
    protected $title = '';

    /**
     * Position de la colonne dans la table
     * @var int
     */
    protected $position = 0;

    /**
     * Affichage du rendu
     * @var string|callable
     */
    protected $content = '';

    /**
     * CONSTRUCTEUR
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     *
     *      @var string $column_name Identifiant de qualification de la colonne
     *      @var string $title Intitulé de la colonne
     *      @var int $position Position de la colonne dans la table
     *      @var string|callable Affichage du rendu de la cellule
     * }
     *
     * @return void
     */
    public function __construct($attrs)
    {
        parent::__construct();

        // Définition des variables d'environnement
        foreach (['column_name', 'title', 'position'] as $attr) :
            if (isset($attrs[$attr])) :
                $Attr = lcfirst($this->appUpperName($attr, false));
                $this->{$Attr} = $attrs[$attr];
            endif;
        endforeach;

        $this->content = !isset($attrs['content']) ? [$this, 'content'] : $attrs['content'];
    }


    /**
     * @param $id
     * @param $object_type
     * @param $object_name
     *
     *
     */
    public function __invoke($id, $object_type, $object_name)
    {
        // Définition des variables d'environnement
        $this->id = $id;
        $this->objectType = $object_type;
        $this->objectName = $object_name;

        // Déclaration des événements
        $this->appAddAction('current_screen');
        switch ($this->getObjectType()) :
            case 'post_type' :
                $this->appAddFilter('manage_edit-' . $this->getObjectName() . '_columns', '_header');
                $this->appAddFilter('manage_' . $this->getObjectName() . '_posts_custom_column', '_content', 25, 2);
                break;

            case 'taxonomy' :
                $this->appAddFilter('manage_edit-' . $this->getObjectName() . '_columns', '_header');
                $this->appAddFilter('manage_' . $this->getObjectName() . '_custom_column', '_content', 25, 3);
                break;

            case 'custom' :
                $this->appAddFilter('manage_' . $this->getObjectName() . '_columns', '_header');
                $this->appAddFilter('manage_' . $this->getObjectName() . '_custom_column', '_content', 25, 3);
                break;
        endswitch;
    }

    /**
     * Chargement de la page courante
     *
     * @param \WP_Screen $current_screen
     *
     * @return void
     */
    public function current_screen($current_screen)
    {
        switch($this->getObjectType()) :
            case 'post_type' :
                $base = 'edit';
                break;
            case 'taxonomy' :
                $base = 'edit-tags';
                break;
        endswitch;

        if ($current_screen->base !== $base) :
            return;
        endif;

        if ($current_screen->post_type !== $this->getObjectName()) :
            return;
        endif;

        // Déclaration de la mise en file des scripts
        $this->appAddAction('admin_enqueue_scripts');
    }

    /**
     * Mise en file des scripts de l'interface d'administration
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {

    }

    /**
     * Déclaration de la colonne
     *
     * @param array $columns Liste de colonnes
     *
     * @return array
     */
    final public function _header($columns)
    {
        if ($position = (int)$this->getPosition()) :
            $newcolumns = []; $n = 0;
            foreach ($columns as $key => $column) :
                if ($n === $position):
                    $newcolumns[$this->getColumnName()] = $this->getTitle();
                endif;
                $newcolumns[$key] = $column;
                $n++;
            endforeach;
            $columns = $newcolumns;
        else :
            $columns[$this->getColumnName()] = $this->getTitle();
        endif;

        return $columns;
    }

    /**
     * Pré-Affichage du contenu de la colonne
     *
     * @return string
     */
    final public function _content()
    {
        switch ($this->getObjectType()) :
            case 'post_type' :
                $column_name = func_get_arg(0);

                // Bypass
                if ($column_name !== $this->getColumnName()) :
                    return '';
                endif;
                break;

            case 'taxonomy' :
                $output         = func_get_arg(0);
                $column_name    = func_get_arg(1);

                // Bypass
                if ($column_name !== $this->getColumnName()) :
                    return $output;
                endif;
                break;

            case 'custom' :
                $output         = func_get_arg(0);
                $column_name    = func_get_arg(1);

                // Bypass
                if ($column_name !== $this->getColumnName()) :
                    return $output;
                endif;
                break;
        endswitch;

        if (is_callable($this->content)) :
            call_user_func_array($this->content, func_get_args());
        elseif(is_string($this->content)) :
            echo $this->content;
        else :
            call_user_func_array([$this, 'content'], func_get_args());
        endif;
    }

    /**
     * Identifiant de qualification de la classe
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Récupération du type d'objet
     *
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Récupération de l'identification du type d'objet
     *
     * @return string
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * Récupération du nom de qualification de la colonne
     *
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName ? : $this->getId();
    }

    /**
     * Récupération de l'intitulé de la colonne
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title ? : $this->getId();
    }

    /**
     * Récupération de la position de la colonne dans la table
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position ? : 0;
    }
}