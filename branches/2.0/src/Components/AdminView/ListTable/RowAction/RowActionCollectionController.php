<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemController;
use tiFy\AdminView\AdminViewInterface;
use tiFy\Components\AdminView\ListTable\Item\ItemInterface;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemActivateController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemDeactivateController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemDeleteController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemDuplicateController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemEditController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemPreviewController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemTrashController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemUntrashController;

class RowActionCollectionController
{
    /**
     * Classe de rappel de la vue associée.
     * @var AdminViewInterface
     */
    protected $view;

    /**
     * Données de l'élément courant.
     * @var ItemInterface
     */
    protected $item = [];

    /**
     * Liste des actions par ligne.
     * @var void|RowActionItemController[]
     */
    protected $rowActions = [];

    /**
     * Liste des controleurs.
     * @var array
     */
    protected $controller = [
        'activate'   => RowActionItemActivateController::class,
        'deactivate' => RowActionItemDeactivateController::class,
        'delete'     => RowActionItemDeleteController::class,
        'duplicate'  => RowActionItemDuplicateController::class,
        'edit'       => RowActionItemEditController::class,
        'preview'    => RowActionItemPreviewController::class,
        'trash'      => RowActionItemTrashController::class,
        'untrash'    => RowActionItemUntrashController::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $row_actions Liste des actions par ligne.
     * @param ItemInterface $item Données de l'élément courant.
     * @param AdminViewInterface $view Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($row_actions, $item, AdminViewInterface $view)
    {
        $this->view = $view;
        $this->item = $item;

        $this->rowActions = $this->parse($row_actions);
    }

    /**
     * Récupération de la liste des actions par ligne.
     *
     * @return array
     */
    public function all()
    {
        return $this->rowActions;
    }

    /**
     * Récupération du controleur
     *
     * @param string $name Nom de qualification de l'action.
     *
     * @return callable
     */
    public function getController($name)
    {
        return isset($this->controller[$name]) ? $this->controller[$name] : RowActionItemController::class;
    }

    /**
     * Traitement de la liste des actions par ligne.
     *
     * @param array $row_actions Liste des actions par ligne.
     *
     * @return void
     */
    public function parse($row_actions = [])
    {
        $_row_actions = [];
        foreach ($row_actions as $name => $attrs) :
            if (is_numeric($name)) :
                $name = $attrs;
                $controller = $this->getController($name);

                $_row_actions[$name] = new $controller($name, [], $this->item, $this->view);
            elseif (is_string($attrs)) :
                $_row_actions[$name] = $attrs;
            else :
                $controller = $this->getController($name);

                $_row_actions[$name] = new $controller($name, $attrs, $this->item, $this->view);
            endif;
        endforeach;

        $_row_actions = array_filter($_row_actions, function ($value) {
            return (string)$value !== '';
        });

        return $_row_actions;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        $actions = $this->all();

        if (!$action_count = count($actions)) :
            return '';
        endif;

        $i = 0;
        $always_visible = $this->view->param('row_actions_always_visible');

        $output = '';
        $output .= "<div class=\"" . ($always_visible ? 'row-actions visible' : 'row-actions') . "\">";
        foreach ($actions as $action => $link) :
            ++$i;
            ($i == $action_count) ? $sep = '' : $sep = ' | ';
            $output .= "<span class=\"{$action}\">{$link}{$sep}</span>";
        endforeach;

        $output .= "</div>";

        $output .= "<button type=\"button\" class=\"toggle-row\"><span class=\"screen-reader-text\">" . __('Show more details') . "</span></button>";

        return $output;
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->display();
    }
}