<?php

namespace tiFy\Components\AdminView\ListTable\Param;

use tiFy\AdminView\Param\ParamCollectionBaseController;

class ParamCollectionController extends ParamCollectionBaseController
{
    /**
     * Liste des paramètres.
     * @var array
     */
    protected $attributes = [
        'edit_base_uri'              => '',
        'columns'                    => [],
        'primary_column'             => [],
        'sortable_columns'           => [],
        'hidden_columns'             => [],
        'preview_item_mode'          => [],
        'preview_item_columns'       => [],
        'preview_item_ajax_args'     => [],
        'table_classes'              => [],
        'per_page'                   => 20,
        'per_page_option_name'       => '',
        'no_items'                   => '',
        'views'                      => [],
        'bulk_actions'               => [],
        'row_actions'                => [],
        'row_actions_always_visible' => false
    ];
}