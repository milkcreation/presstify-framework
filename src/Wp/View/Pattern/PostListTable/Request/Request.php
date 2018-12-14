<?php

namespace tiFy\Wp\View\Pattern\PostListTable\Request;

use tiFy\View\Pattern\ListTable\Request\Request as BaseListTableRequest;

class Request extends BaseListTableRequest
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