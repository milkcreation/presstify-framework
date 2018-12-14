<?php

namespace tiFy\View\Pattern\ListTable\Params;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;
use tiFy\View\Pattern\ListTable\Contracts\Params as ParamsContract;

class Params extends ParamsBag implements ParamsContract
{
    /**
     * Instance de la disposition associée.
     * @var ListTable
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des paramètres personnalisés.
     *
     * @return void
     */
    public function __construct($attrs, ListTable $pattern)
    {
        $this->pattern = $pattern;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'edit_base_uri'              => '',
            'bulk_actions'               => [],
            'columns'                    => [],
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