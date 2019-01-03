<?php

namespace tiFy\Partial\Partials\Pagination;

use tiFy\Partial\PartialView;

/**
 * Class PartialView
 *
 * @method string getPagenumLink(int $num)
 */
class PaginationView extends PartialView
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [
        'after',
        'attrs',
        'before',
        'content',
        'getId',
        'getIndex',
        'getPagenumLink'
    ];

    /**
     * Récupération de la page courante.
     *
     * @return mixed
     */
    public function getPage()
    {
        return $this->get('query')->getPage();
    }

    /**
     * Récupération du nombre total de page.
     *
     * @return mixed
     */
    public function getTotalPage()
    {
        return $this->get('query')->getTotalPage();
    }
}