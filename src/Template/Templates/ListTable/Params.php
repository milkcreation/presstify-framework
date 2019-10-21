<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Template\Factory\FactoryParams;
use tiFy\Template\Templates\ListTable\Contracts\{ListTable, Params as ParamsContract};

class Params extends FactoryParams implements ParamsContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * @inheritdoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'attrs'                      => [
                'class' => '%s'
            ],
            'edit_base_uri'              => '',
            'bulk-actions'               => [],
            //'columns'                    => [],
            'column_primary'             => '',
            'primary_key'                => '',
            'preview_item_mode'          => [],
            'preview_item_columns'       => [],
            'preview_item_ajax_args'     => [],
            'search'                     => true,
            'table_classes'              => '%s',
            'view-filters'               => [],
            'row-actions'                => [],
            'row_actions_always_visible' => false
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parse(): Params
    {
        parent::parse();

        $class = trim(sprintf(
            $this->get('attrs.class'), 'wp-list-table widefat fixed striped ' . $this->get('plural'))
        );
        $this->set('attrs.class', $class);

        return $this;
    }
}