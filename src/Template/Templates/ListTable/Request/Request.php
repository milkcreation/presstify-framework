<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Request;

use tiFy\Template\Factory\FactoryRequest;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use tiFy\Template\Templates\ListTable\Contracts\Request as RequestContract;

class Request extends FactoryRequest implements RequestContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

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
     * @inheritdoc
     */
    public function getPerPage(): int
    {
        if (is_null($this->perPage)) {
            $option_name = $this->factory->param('per_page_option_name');
            $default = $this->factory->param('per_page', 20);

            $per_page = (int)get_user_option($option_name);
            if (empty($per_page) || $per_page < 1) {
                $per_page = $default;
            }

            $this->perPage = (int)apply_filters("{$option_name}", $per_page);
        }

        return $this->perPage;
    }

    /**
     * @inheritdoc
     */
    public function getPagenum(): int
    {
        if (is_null($this->pageNum)) {
            $pagenum = ($this->factory->ajax() && $this->get('draw'))
                ? ceil(($this->get('start', 0)/$this->get('length', 0))+1)
                : $this->get('paged', 0);

            /*if ($pagenum > $this->getTotalPages()) {
                $pagenum = $this->getTotalPages();
            }*/

            $this->pageNum = max(1, $pagenum);
        }
        return (int)$this->pageNum;
    }

    /**
     * @inheritdoc
     */
    public function getQueryArgs(): array
    {
        $query_args = $this->factory->param('query_args', []);

        if (!$db = $this->factory->db()) {
            return $query_args;
        }

        $per_page = $this->getPerPage();
        $paged = $this->getPagenum();

        $query_args = array_merge([
            'per_page' => $per_page,
            'paged'    => $paged,
            'order'    => 'DESC',
            'orderby'  => $db->getPrimary()
        ], $query_args);

        if ($this->factory->ajax()) {
            if ($query_args['draw'] = $this->get('draw', 0)) {
                if ($length = $this->get('length', 0)) {
                    $query_args['per_page'] = $length;
                }
                /*
                if (isset($_REQUEST['search']) && isset($_REQUEST['search']['value'])) {
                    $query_args['search'] = $_REQUEST['search']['value'];
                }

                if (isset($_REQUEST['order'])) {
                    $query_args['orderby'] = [];
                }

                foreach ((array)$_REQUEST['order'] as $k => $v) {
                    $query_args['orderby'][$_REQUEST['columns'][$v['column']]['data']] = $v['dir'];
                }
                */
            }
        }


        return $query_args;
    }

    /**
     * @inheritdoc
     */
    public function searchExists(): bool
    {
        return !empty($this->get('s'));
    }

    /**
     * @inheritdoc
     */
    public function searchTerm(): string
    {
        return $this->searchExists() ? esc_attr(wp_unslash($this->get('s'))) : '';
    }
}