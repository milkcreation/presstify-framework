<?php

namespace tiFy\Components\Layout\AjaxListTable\Request;

use tiFy\Components\Layout\ListTable\Request\RequestController as ListTableRequestController;

class RequestController extends ListTableRequestController
{
    /**
     * {@inheritdoc}
     */
    public function getPagenum()
    {
        if (is_null($this->pageNum)) :
            if (!$this->get('draw')) :
                $pagenum = $this->get('paged', 0);
            else :
                $pagenum = ceil(($this->get('start', 0)/$this->get('length', 0))+1);
            endif;

            $this->pageNum = max(1, $pagenum);
        endif;

        return $this->pageNum;
    }

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
                'orderby'  => $db->getPrimary(),
            ],
            $query_args
        );

        if ($query_args['draw'] = $this->get('draw', 0)) :
            if ($length = $this->get('length', 0)) :
                $query_args['per_page'] = $length;
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