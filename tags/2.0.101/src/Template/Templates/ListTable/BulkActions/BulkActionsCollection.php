<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\BulkActions;

use tiFy\Support\Collection;
use tiFy\Template\Templates\ListTable\Contracts\BulkActionsCollection as BulkActionsCollectionContract;
use tiFy\Template\Templates\ListTable\Contracts\BulkActionsItem;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class BulkActionsCollection extends Collection implements BulkActionsCollectionContract
{
    /**
     * Compteur d'instance d'affichage.
     * @var int
     */
    protected static $displayed = 0;

    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Liste des actions groupées.
     * @var array|BulkActionsItem[]
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
     * @param ListTable $factory Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(ListTable $factory)
    {
        $this->factory = $factory;

        $attrs = $this->factory->param('bulk_actions', []);

        $this->parse(is_array($attrs) ? $attrs : []);
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return (string)$this->render();
    }

    /**
     * @inheritdoc
     *
     * @return array|BulkActionsItem[]
     */
    public function all()
    {
        return parent::all();
    }

    /**
     * @inheritdoc
     */
    public function parse(array $bulk_actions = []): BulkActionsCollectionContract
    {
        if ($bulk_actions) {
            $this->items[-1] = $this->factory->resolve('bulk-actions.item', [
                -1,
                ['content' => __('Actions groupées', 'tify')],
                $this->factory
            ]);

            foreach ($bulk_actions as $name => $attrs) {
                if (is_numeric($name)) {
                    $name = (string)$attrs;
                    $attrs = [];
                } elseif (is_string($attrs)) {
                    $attrs = [
                        'value'   => $name,
                        'content' => $attrs
                    ];
                }

                $alias = $this->factory->bound("bulk-actions.item.{$name}")
                    ? "bulk-actions.item.{$name}"
                    : 'bulk-actions.item';

                $this->items[$name] = $this->factory->resolve($alias, [$name, $attrs, $this->factory]);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        $output = '';

        if ($choices = $this->all()) {
            $displayed = !self::$displayed++ ? '' : 2;

            $output .= field('label', [
                'attrs'   => [
                    'for'   => 'bulk-action-selector-' . esc_attr($this->which),
                    'class' => 'screen-reader-text'
                ],
                'content' => __('Choix de l\'action', 'tify')
            ]);

            $output .= field('select', [
                'name'    => "action{$displayed}",
                'attrs'   => [
                    'id' => 'bulk-action-selector-' . esc_attr($this->which)
                ],
                'choices' => $choices
            ]);

            $output .= field('submit', [
                'attrs' => [
                    'id'    => "doaction{$displayed}",
                    'value' => __('Apply'),
                    'class' => 'button action'
                ]
            ]);
        }

        return $output;
    }

    /**
     * @inheritdoc
     */
    public function which(string $which) : BulkActionsCollectionContract
    {
        $this->which = $which;

        return $this;
    }
}