<?php

namespace tiFy\Components\Layout\ListTable\Request;

use Illuminate\Http\Request;
use tiFy\Components\Layout\ListTable\ListTableInterface;
use tiFy\App\Layout\Request\RequestBaseController;

class RequestController extends RequestBaseController implements RequestInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var ListTableInterface
     */
    protected $app;

    /**
     * Nombre d'élément affichés par page.
     * @var null|int
     */
    protected $perPage;

    /**
     * Numero de la page d'affichage courant.
     * @var null|int
     */
    protected $pageNum;

    /**
     * {@inheritdoc}
     */
    public function getPerPage()
    {
        if (is_null($this->perPage)) :
            $option_name = $this->app->param('per_page_option_name');
            $default = $this->app->param('per_page', 20);

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

        return $query_args;
    }
}