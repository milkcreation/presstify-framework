<?php

namespace tiFy\Components\Layout\ListTable\BulkAction;

use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemController;
use tiFy\Components\Layout\ListTable\ListTableInterface;
use tiFy\Field\Field;
use tiFy\Field\FieldOptionsItemController;

class BulkActionCollectionController implements BulkActionCollectionInterface
{
    /**
     * Compteur d'instance d'affichage.
     * @var int
     */
    protected static $displayed = 0;

    /**
     * Classe de rappel de la vue associée.
     * @var ListTableInterface
     */
    protected $app;

    /**
     * Liste des actions groupées.
     * @var void|BulkActionItemInterface[]
     */
    protected $items = [];

    /**
     * Position de l'interface de navigation.
     * @var string
     */
    protected $which = 'top';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $which Position de l'interface de navigation. top|bottom.
     * @param ListTableInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($which, ListTableInterface $app)
    {
        $this->app = $app;
        $this->which = $which;
        $this->parse($this->app->param('bulk_actions', []));
    }

    /**
     * Récupération de la liste des actions groupées.
     *
     * @return void|BulkActionItemInterface[]
     */
    public function all()
    {
        return $this->items;
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
        if ($bulk_actions) :
            foreach ($bulk_actions as $name => $attrs) :
                if (is_numeric($name)) :
                    $name = (string)$attrs;
                    $attrs = [];
                elseif (is_string($attrs)) :
                    $attrs = [
                        'value'   => $name,
                        'content' => $attrs
                    ];
                endif;

                $alias = $this->app->bound("bulk_actions.item.{$name}")
                    ? "bulk_actions.item.{$name}"
                    : BulkActionItemInterface::class;

                $this->items[$name] = $this->app->resolve($alias, [$name, $attrs]);
            endforeach;
        endif;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        if (!$items = $this->all()) :
            return '';
        endif;

        $options = [
            -1 => __('Actions groupées', 'theme')
        ];
        foreach ($items as $item) :
            $options[] = new FieldOptionsItemController($item->getName(), $item->all());
        endforeach;

        $displayed = ! self::$displayed++ ? '' : 2;

        $output = '';
        $output .= Field::Label(
            [
                'attrs'   => [
                    'for'   => 'bulk-action-selector-' . esc_attr($this->which),
                    'class' => 'screen-reader-text'
                ],
                'content' => __('Choix de l\'action', 'tify')
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