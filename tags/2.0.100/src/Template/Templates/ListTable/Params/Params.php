<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Params;

use tiFy\Template\Factory\FactoryParams;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class Params extends FactoryParams
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return array_merge(parent::defaults(), [
            'attrs'                      => [
                'class' => '%s'
            ],
            'edit_base_uri'              => '',
            'bulk_actions'               => [],
            'columns'                    => [],
            'colum_primary'              => '',
            'item_primary_key'           => '',
            'per_page'                   => 20,
            'per_page_option_name'       => '',
            'preview_item_mode'          => [],
            'preview_item_columns'       => [],
            'preview_item_ajax_args'     => [],
            'table_classes'              => '%s',
            'view_filters'               => [],
            'row_actions'                => [],
            'row_actions_always_visible' => false
        ]);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        $class = trim(sprintf(
            $this->get('attrs.class'), 'wp-list-table widefat fixed striped ' . $this->get('plural'))
        );
        $this->set('attrs.class', $class);
        $this->set('attrs.data-control', $this->get('ajax') ? 'list-table.ajax': 'list-table');

        return $this;
    }
}