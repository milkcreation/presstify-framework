<?php

namespace tiFy\Partial\Pagination;

use tiFy\Partial\PartialView;

/**
 * Class PartialView
 *
 * @method string after().
 * @method string attrs().
 * @method string before().
 * @method string content().
 * @method string getHtmlAttrs().
 * @method string getId().
 * @method string getIndex().
 * @method string getPagenumLink(int $num).
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
        'getHtmlAttrs',
        'getId',
        'getIndex',
        'getPagenumLink'
    ];

    /**
     * Boucle d'affichage des numéros de page.
     *
     * @return string
     */
    public function numLoop($start, $end)
    {
        for ($num = $start; $num <= $end; $num++) :
            $this->insert(
                'num',
                array_merge(
                    $this->all(),
                    ['num' => $num]
                )
            );
        endfor;
    }
}