<?php

namespace tiFy\Components\Layout\ListTable\Param;

use tiFy\Apps\Layout\Param\ParamCollectionBaseController;

class ParamCollectionController extends ParamCollectionBaseController
{
    /**
     * Liste des paramètres.
     * @var array
     */
    protected $attributes = [
        'edit_base_uri'              => '',
        'bulk_actions'               => [],
        'columns'                    => [],
        'no_items'                   => '',
        'page_title'                 => '',
        'per_page'                   => 20,
        'per_page_option_name'       => '',
        'preview_item_mode'          => [],
        'preview_item_columns'       => [],
        'preview_item_ajax_args'     => [],
        'table_classes'              => [],
        'view_filters'               => [],
        'row_actions'                => [],
        'row_actions_always_visible' => false
    ];

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'page_title' => $this->app->getLabel('all_items', ''),
            'no_items'   => __('No items found.')
        ];
    }
}