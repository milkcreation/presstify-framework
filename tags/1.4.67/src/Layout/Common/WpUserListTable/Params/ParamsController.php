<?php

namespace tiFy\Components\Layout\UserListTable\Params;

use tiFy\Components\Layout\ListTable\Params\ParamsController as ListTableParamsController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemRoleController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemUserLoginController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemUserRegisteredController;

class ParamsController extends ListTableParamsController
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
                    'user_login'      => [
                        'controller' => ColumnItemUserLoginController::class,
                    ],
                    'display_name'    => __('Nom', 'tify'),
                    'user_email'      => __('E-mail', 'tify'),
                    'user_registered' => [
                        'controller' => ColumnItemUserRegisteredController::class,
                    ],
                    'role'            => [
                        'controller' => ColumnItemRoleController::class,
                    ],
                ],
                'bulk_actions' => ['trash'],
                'row_actions'  => ['edit', 'delete']
            ]
        );
    }
}