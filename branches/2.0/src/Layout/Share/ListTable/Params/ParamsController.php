<?php

namespace tiFy\Layout\Share\ListTable\Params;

use tiFy\Layout\Base\ParamsBaseController;

class ParamsController extends ParamsBaseController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'edit_base_uri'              => '',
            'bulk_actions'               => [],
            'columns'                    => [],
            'no_items'                   => __('No items found.'),
            'page_title'                 => '',
            'page_title'                 => $this->layout->label('all_items', ''),
            'per_page'                   => 20,
            'per_page_option_name'       => '',
            'preview_item_mode'          => [],
            'preview_item_columns'       => [],
            'preview_item_ajax_args'     => [],
            'table_classes'              => '%s',
            'view_filters'               => [],
            'row_actions'                => [],
            'row_actions_always_visible' => false
        ];
    }
}