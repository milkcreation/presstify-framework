<?php

namespace tiFy\Layout\Share\WpUserListTable\Params;

use tiFy\Layout\Share\ListTable\Params\ParamsController as ShareListTableParamsController;

class ParamsController extends ShareListTableParamsController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return  array_merge(
            parent::defaults(),
            [
                'columns'      => [
                    'cb',
                    'user_login',
                    'display_name'    => __('Nom', 'tify'),
                    'user_email'      => __('E-mail', 'tify'),
                    'user_registered',
                    'role'
                ],
                'bulk_actions' => ['trash'],
                'row_actions'  => ['edit', 'delete']
            ]
        );
    }
}