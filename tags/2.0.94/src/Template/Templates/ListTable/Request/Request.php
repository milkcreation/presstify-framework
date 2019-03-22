<?php

namespace tiFy\Template\Templates\ListTable\Request;

use tiFy\Template\Templates\BaseRequest;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use tiFy\Template\Templates\ListTable\Contracts\Request as RequestContract;

class Request extends BaseRequest implements RequestContract
{
    /**
     * Instance de la disposition associée.
     * @var ListTable
     */
    protected $template;

    /**
     * Numero de la page d'affichage courant.
     * @var null|int
     */
    protected $pageNum;

    /**
     * Nombre d'élément affichés par page.
     * @var null|int
     */
    protected $perPage;

    /**
     * {@inheritdoc}
     */
    public function getPerPage()
    {
        if (is_null($this->perPage)) :
            $option_name = $this->template->param('per_page_option_name');
            $default = $this->template->param('per_page', 20);

            $per_page = (int)get_user_option($option_name);
            if (empty($per_page) || $per_page < 1) :
                $per_page = $default;
            endif;

            $this->perPage = (int)apply_filters("{$option_name}", $per_page);
        endif;

        return $this->perPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getPagenum()
    {
        if (is_null($this->pageNum)) :
            $pagenum = (int)$this->get('paged', 0);

            /*if ($pagenum > $this->getTotalPages()) :
                $pagenum = $this->getTotalPages();
            endif;*/

            $this->pageNum = max(1, $pagenum);
        endif;

        return $this->pageNum;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryArgs()
    {
        $query_args = $this->template->param('query_args', []);

        if (!$db = $this->template->db()) :
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

        return $query_args;
    }

    /**
     * {@inheritdoc}
     */
    public function searchExists()
    {
        return !empty($this->get('s'));
    }

    /**
     * {@inheritdoc}
     */
    public function searchTerm()
    {
        return $this->searchExists() ? esc_attr(wp_unslash($this->get('s'))) : '';
    }
}