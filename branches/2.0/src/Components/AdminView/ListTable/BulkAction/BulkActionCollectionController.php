<?php

namespace tiFy\Components\AdminView\ListTable\BulkAction;

use tiFy\Components\AdminView\ListTable\BulkAction\BulkActionItemController;
use tiFy\AdminView\AdminViewInterface;
use tiFy\Field\Field;

class BulkActionCollectionController
{
    /**
     * Compteur d'instance d'affichage.
     * @var int
     */
    protected static $displayed = 0;

    /**
     * Classe de rappel de la vue associée.
     * @var AdminViewInterface
     */
    protected $view;

    /**
     * Liste des actions groupées.
     * @var void|BulkActionItemController[]
     */
    protected $bulk_actions = [];

    /**
     * Position de l'interface de navigation.
     * @var string
     */
    protected $which = 'top';

    /**
     * CONSTRUCTEUR.
     *
     * @param array $bulk_actions Liste des actions groupées.
     * @param string $which Position de l'interface de navigation. top|bottom.
     * @param AdminViewInterface $view Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($bulk_actions, $which, AdminViewInterface $view)
    {
        $this->view = $view;
        $this->which = $which;
        $this->bulk_actions = $this->parse($bulk_actions);
    }

    /**
     * Récupération de la liste des actions groupées.
     *
     * @return array
     */
    public function all()
    {
        return $this->bulk_actions;
    }

    /**
     * Traitement de la liste des actions groupées.
     *
     * @param array $bulk_actions Liste des actions groupées.
     *
     * @return void
     */
    public function parse($bulk_actions = [])
    {
        $_bulk_actions = [];
        foreach ($bulk_actions as $name => $attrs) :
            if (is_numeric($name)) :
                $value = $name;
                $name = (string)$attrs;
                $attrs = compact('value');
            elseif (is_string($attrs)) :
                $attrs = [
                    'value'   => $name,
                    'content' => $attrs
                ];
            endif;

            if ($attrs = (new BulkActionItemController($name, $attrs, $this->view))->all()) :
                $_bulk_actions[] = $attrs;
            endif;
        endforeach;

        return $_bulk_actions;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        if (!$options = $this->bulk_actions) :
            return '';
        endif;

        array_unshift(
            $options,
            [
                'value'   => -1,
                'content' => __('Bulk Actions')
            ]
        );
        $displayed = ! self::$displayed++ ? '' : 2;

        $output = '';
        $output .= Field::Label(
            [
                'attrs'   => [
                    'for'   => 'bulk-action-selector-' . esc_attr($this->which),
                    'class' => 'screen-reader-text'
                ],
                'content' => __('Select bulk action')
            ]
        );

        $output .= Field::Select(
            [
                'name'    => "action{$displayed}",
                'attrs'   => [
                    'id' => 'bulk-action-selector-' . esc_attr($this->which)
                ],
                'options' => $options
            ]
        );

        $output .= Field::Submit(
            [
                'attrs' => [
                    'id'    => "doaction{$displayed}",
                    'value' => __('Apply'),
                    'class' => 'button action'
                ]
            ]
        );

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