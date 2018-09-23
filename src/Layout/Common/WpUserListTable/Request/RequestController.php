<?php

namespace tiFy\Components\Layout\UserListTable\Request;

use tiFy\Components\Layout\ListTable\Request\RequestController as ListTableRequestController;

class RequestController extends ListTableRequestController
{
    /**
     * {@inheritdoc}
     */
    public function getQueryArgs()
    {
        $query_args = $this->app->param('query_args', []);

        if (!$db = $this->app->db()) :
            return $query_args;
        endif;

        $query_args = array_merge(
            [
                'number'      => $this->getPerPage(),
                'paged'       => $this->getPagenum(),
                'count_total' => true,
                'fields'      => 'all_with_meta',
                'orderby'     => 'user_registered',
                'order'       => 'DESC',
                'role__in'    => $this->app->param('roles', []),
                //'include'     => $this->current_item_index()
            ],
            $query_args
        );

        return $query_args;
    }
}