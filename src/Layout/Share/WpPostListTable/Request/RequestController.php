<?php

namespace tiFy\Layout\Share\WpPostListTable\Request;

use tiFy\Layout\Share\ListTable\Request\RequestController as ListTableRequestController;

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