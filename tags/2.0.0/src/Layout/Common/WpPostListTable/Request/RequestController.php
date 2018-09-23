<?php

namespace tiFy\Components\Layout\PostListTable\Request;

use tiFy\Components\Layout\ListTable\Request\RequestController as ListTableRequestController;

class RequestController extends ListTableRequestController
{
    /**
     * {@inheritdoc}
     */
    public function getQueryArgs()
    {
        $query_args = parent::getQueryArgs();

        if ($post_status = $this->get('post_status')) :
            $query_args['post_status'] = $post_status;
        endif;

        return $query_args;
    }
}