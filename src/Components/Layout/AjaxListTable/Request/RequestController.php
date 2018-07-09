<?php

namespace tiFy\Components\Layout\AjaxListTable\Request;

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

        $per_page = $this->getPerPage();
        $paged = $this->getPagenum();

        $query_args = array_merge(
            [
                'per_page' => $per_page,
                'paged'    => $paged,
                'order'    => 'DESC',
                'orderby'  => $db->getPrimary()
            ],
            $query_args
        );

        if ($draw = $this->app->appRequest('GET')->get('draw')) :
            $query_args['draw'] = $draw;

            if ($length = $this->app->appRequest('GET')->get('length')) :
                $query_args['per_page'] = $length;
            endif;

            if ($length && ($start = $this->app->appRequest('GET')->get('start'))) :
                $query_args['paged'] = /*$query_args['page'] =*/ ceil(($start / $length) + 1);
                $this->app->appRequest('GET')->set('paged', $query_args['paged']);
                $this->app->appRequest('GET')->set('page', $query_args['paged']);
            endif;
            /*
            if (isset($_REQUEST['search']) && isset($_REQUEST['search']['value'])) :
                $query_args['search'] = $_REQUEST['search']['value'];
            endif;

            if (isset($_REQUEST['order'])) :
                $query_args['orderby'] = [];
            endif;

            foreach ((array)$_REQUEST['order'] as $k => $v) :
                $query_args['orderby'][$_REQUEST['columns'][$v['column']]['data']] = $v['dir'];
            endforeach;
            */
        endif;

        return $query_args;
    }
}