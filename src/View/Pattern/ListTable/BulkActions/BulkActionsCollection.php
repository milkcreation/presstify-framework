<?php

namespace tiFy\View\Pattern\ListTable\BulkActions;

use tiFy\Kernel\Collection\Collection;
use tiFy\View\Pattern\ListTable\Contracts\BulkActionsCollection as BulkActionsCollectionContract;
use tiFy\View\Pattern\ListTable\Contracts\BulkActionsItem;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;

class BulkActionsCollection extends Collection implements BulkActionsCollectionContract
{
    /**
     * Compteur d'instance d'affichage.
     * @var int
     */
    protected static $displayed = 0;

    /**
     * Liste des actions groupées.
     * @var array|BulkActionsItem[]
     */
    protected $items = [];

    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $pattern;

    /**
     * Position de l'interface de navigation.
     * @var string
     */
    protected $which = 'top';

    /**
     * CONSTRUCTEUR.
     *
     * @param array $bulk_actions Liste des des éléments.
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($bulk_actions, ListTable $pattern)
    {
        $this->pattern = $pattern;

        $this->parse($bulk_actions);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * {@inheritdoc}
     *
     * @return array|BulkActionsItem[]
     */
    public function all()
    {
        return parent::all();
    }

    /**
     * {@inheritdoc}
     */
    public function parse($bulk_actions = [])
    {
        if ($bulk_actions) :
            $this->items[-1] = $this->pattern->resolve(
                'bulk-actions.item',
                [-1, ['content' =>  __('Actions groupées', 'tify')],
                 $this->pattern]
            );

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

                $alias = $this->pattern->bound("bulk-actions.item.{$name}")
                    ? "bulk-actions.item.{$name}"
                    : 'bulk-actions.item';

                $this->items[$name] = $this->pattern->resolve($alias, [$name, $attrs, $this->pattern]);
            endforeach;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $output = '';

        if ($options = $this->all()) :
            $displayed = !self::$displayed++ ? '' : 2;

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
        endif;

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function which($which)
    {
        $this->which = $which;

        return $this;
    }
}