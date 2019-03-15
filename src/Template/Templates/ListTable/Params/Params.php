<?php

namespace tiFy\Template\Templates\ListTable\Params;

use tiFy\Template\Templates\BaseParams;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class Params extends BaseParams
{
    /**
     * Instance de la disposition associée.
     * @var ListTable
     */
    protected $template;

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return array_merge(
            parent::defaults(),
            [
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
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set(
            'attrs.class',
            trim(sprintf($this->get('attrs.class'), 'wp-list-table widefat fixed striped ' . $this->get('plural')))
        );
    }
}