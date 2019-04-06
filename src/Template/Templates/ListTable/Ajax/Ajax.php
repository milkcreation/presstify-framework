<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Ajax;

use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Routing\Route;
use tiFy\Support\ParamsBag;
use tiFy\Template\Templates\ListTable\Contracts\Ajax as AjaxContract;
use tiFy\Template\Templates\ListTable\Contracts\ColumnsItem;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class Ajax extends ParamsBag implements AjaxContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Instance de la route XHR associée.
     * @var Route
     */
    protected $xhr;

    /**
     * CONSTRUCTEUR.
     *
     * @param ListTable $factory Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(ListTable $factory)
    {
        $this->factory = $factory;

        $attrs = $this->factory->param('ajax', []);

        $this->set(is_array($attrs) ? $attrs : []);
    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'ajax'        => [
                'url'      => $this->xhr->getUrl(),
                'dataType' => 'json',
                'type'     => 'POST',
            ],
            'data'        => [],
            'columns'     => $this->getColumns(),
            'language'    => $this->getLanguage(),
            'options'     => [
                'pageLength' => $this->factory->pagination()->getPerPage()
            ],
            'total_items' => $this->factory->pagination()->getTotalItems(),
            'total_pages' => $this->factory->pagination()->getTotalPages(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getColumns(): array
    {
        $cols = [];
        foreach ($this->factory->columns() as $name => $c) {
            /** @var ColumnsItem $c */
            array_push($cols, [
                'data'      => $c->getName(),
                'name'      => $c->getName(),
                'title'     => $c->getTitle(),
                'orderable' => false,
                'visible'   => $c->isVisible()
            ]);
        }
        return $cols;
    }

    /**
     * @inheritdoc
     */
    public function getLanguage(): array
    {
        return [
            'processing'     => __('Traitement en cours...', 'tify'),
            'search'         => __('Rechercher&nbsp;:', 'tify'),
            'lengthMenu'     => __('Afficher _MENU_ &eacute;l&eacute;ments', 'tify'),
            'info'           => __('Affichage de l\'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ ' .
                '&eacute;l&eacute;ments', 'tify'),
            'infoEmpty'      => __('Affichage de l\'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments', 'tify'),
            'infoFiltered'   => __('(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)', 'tify'),
            'infoPostFix'    => __('', 'tify'),
            'loadingRecords' => __('Chargement en cours...', 'tify'),
            'zeroRecords'    => __('Aucun &eacute;l&eacute;ment &agrave; afficher', 'tify'),
            'emptyTable'     => __('Aucune donnée disponible dans le tableau', 'tify'),
            'paginate'       => [
                'first'    => __('Premier', 'tify'),
                'previous' => __('Pr&eacute;c&eacute;dent', 'tify'),
                'next'     => __('Suivant', 'tify'),
                'last'     => __('Dernier', 'tify'),
            ],
            'aria'           => [
                'sortAscending'  => __(': activer pour trier la colonne par ordre croissant', 'tify'),
                'sortDescending' => __(': activer pour trier la colonne par ordre décroissant', 'tify')
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        $this->set('options', $this->parseOptions($this->get('options', [])));

        $this->factory->param()->set('attrs.data-options', $this->all());

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function parseOptions(array $options = []): array
    {
        return array_diff_key((is_array($options) ? $options : []), array_flip([
            'ajax',
            'drawCallback',
            'deferLoading',
            'initComplete',
            'processing',
            'serverSide'
        ]));
    }

    /**
     * @inheritdoc
     */
    public function setXhr(Route $route): AjaxContract
    {
        $this->xhr = $route;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function xhrHandler(ServerRequestInterface $psrRequest)
    {
        $this->factory->load();

        $cols = [];
        if ($this->factory->items()->exists()) {
            foreach ($this->factory->items() as $i => $item) {
                foreach ($this->factory->columns() as $name => $col) {
                    $cols[$i][$name] = $col->render();
                }
            }
        }

        return [
            'data'            => $cols,
            'draw'            => $this->factory->request()->post('draw'),
            'pagenum'         => $this->factory->request()->getPageNum(),
            'pagination'      => (string)$this->factory->viewer('pagination', ['which' => 'bottom']),
            'recordsTotal'    => $this->factory->pagination()->getTotalItems(),
            'recordsFiltered' => $this->factory->pagination()->getTotalItems(),
            'search_form'     => (string)$this->factory->viewer('search-box')
        ];
    }
}