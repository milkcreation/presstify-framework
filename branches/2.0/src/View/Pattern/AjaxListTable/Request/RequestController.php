<?php

namespace tiFy\Layout\Share\AjaxListTable\Request;

use tiFy\Layout\Share\ListTable\Request\RequestController as ShareListTableRequestController;

class RequestController extends ShareListTableRequestController
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
        $query_args = $this->layout->param('query_args', []);

        if (!$db = $this->layout->db()) :
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