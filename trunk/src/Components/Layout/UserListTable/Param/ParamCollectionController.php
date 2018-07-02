<?php

namespace tiFy\Components\Layout\UserListTable\Param;

use tiFy\Components\Layout\ListTable\Param\ParamCollectionController as ListTableParamCollectionController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemRoleController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemUserLoginController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemUserRegisteredController;

class ParamCollectionController extends ListTableParamCollectionController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $attrs = array_merge(
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

        return $attrs;
    }
}