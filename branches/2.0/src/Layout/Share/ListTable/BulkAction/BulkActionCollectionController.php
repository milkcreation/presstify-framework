<?php

namespace tiFy\Layout\Share\ListTable\BulkAction;

use tiFy\Layout\Share\ListTable\Contracts\BulkActionCollectionInterface;
use tiFy\Layout\Share\ListTable\Contracts\BulkActionItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;
use tiFy\Field\FieldOptionsItemController;

class BulkActionCollectionController implements BulkActionCollectionInterface
{
    /**
     * Compteur d'instance d'affichage.
     * @var int
     */
    protected static $displayed = 0;

    /**
     * Instance de la disposition associée.
     * @var ListTableInterface
     */
    protected $layout;

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
     * @param ListTableInterface $layout Instance de la disposition associée.
     *
     * @return void
     */
    public function __construct($which, ListTableInterface $layout)
    {
        $this->layout = $layout;
        $this->which = $which;
        $this->parse($this->layout->param('bulk_actions', []));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->display();
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
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
        $output .= field(
            'label',
            [
                'attrs'   => [
                    'for'   => 'bulk-action-selector-' . esc_attr($this->which),
                    'class' => 'screen-reader-text'
                ],
                'content' => __('Choix de l\'action', 'tify')
            ]
        );

        $output .= field(
            'select',
            [
                'name'    => "action{$displayed}",
                'attrs'   => [
                    'id' => 'bulk-action-selector-' . esc_attr($this->which)
                ],
                'options' => $options
            ]
        );

        $output .= field(
            'submit',
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
     * {@inheritdoc}
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

                $alias = $this->layout->bound("layout.bulk_actions.item.{$name}")
                    ? "bulk_actions.item.{$name}"
                    : 'bulk_action.item';

                $this->items[$name] = $this->layout->resolve($alias, [$name, $attrs, $this->layout]);
            endforeach;
        endif;
    }
}