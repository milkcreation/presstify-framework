<?php

namespace tiFy\Layout\Share\WpUserListTable\Request;

use tiFy\Layout\Share\ListTable\Request\RequestController as ShareListTableRequestController;

class RequestController extends ShareListTableRequestController
{
    /**
     * {@inheritdoc}
     */
    public function getQueryArgs()
    {
        $query_args = $this->layout->param('query_args', []);

        if (!$db = $this->layout->db()) :
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
                'role__in'    => $this->layout->param('roles', []),
                //'include'     => $this->current_item_index()
            ],
            $query_args
        );

        return $query_args;
    }
}