<?php

namespace tiFy\View\Pattern\ListTable\Pagination;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\View\Pattern\ListTable\Contracts\Pagination as PaginationContract;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;

class Pagination extends ParamsBag implements PaginationContract
{
    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [
        'total_items' => 0,
        'total_pages' => 0,
        'per_page'    => 0,
    ];

    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des paramètres.
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($attrs, ListTable $pattern)
    {
        $this->pattern = $pattern;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function currentPage()
    {
        $total_pages_before = '<span class="paging-input">';
        $total_pages_after = '</span></span>';

        if ('bottom' === $this->which) :
            $html_current_page = $this->pageNum();
            $total_pages_before = '<span class="screen-reader-text">' . __('Page courante',
                    'tify') . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        else :
            $html_current_page = sprintf("%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector" class="screen-reader-text">' . __('Page courante',
                    'tify') . '</label>',
                $this->pageNum(),
                strlen($this->getTotalPages())
            );
        endif;

        $html_total_pages = sprintf("<span class='total-pages'>%s</span>", number_format_i18n($this->getTotalPages()));

        return $total_pages_before .
            sprintf(
                _x('%1$s sur %2$s', 'paging', 'tify'),
                $html_current_page,
                $this->getTotalPages()
            ) .
            $total_pages_after;
    }

    /**
     * {@inheritdoc}
     */
    public function firstPage()
    {
        return $this->pattern->viewer(
            'pagination-first',
            [
                'disabled'   => $this->isDisableFirst(),
                'url'        => $this->unpagedUrl(),
                'pagination' => $this,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        $classes = [];
        $classes[] = 'tablenav-pages';

        $classes[] = ($total_pages = $this->getTotalPages())
            ? ($total_pages < 2 ? 'one-page' : '')
            : 'no-pages';

        return join(' ', $classes);
    }

    /**
     * {@inheritdoc}
     */
    public function getPerPage()
    {
        return intval($this->get('per_page', 0));
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalItems()
    {
        return intval($this->get('total_items', 0));
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalPages()
    {
        return intval($this->get('total_pages', 0));
    }

    /**
     * {@inheritdoc}
     */
    public function isDisableFirst()
    {
        return ($this->pageNum() === 1 || $this->pattern->request()->getPagenum() === 2);
    }

    /**
     * {@inheritdoc}
     */
    public function isDisableLast()
    {
        return ($this->pageNum() >= $this->getTotalPages() - 1);
    }

    /**
     * {@inheritdoc}
     */
    public function isDisableNext()
    {
        return ($this->pageNum() === $this->getTotalPages());
    }

    /**
     * {@inheritdoc}
     */
    public function isDisablePrev()
    {
        return ($this->pageNum() === 1);
    }

    /**
     * {@inheritdoc}
     */
    public function isInfiniteScroll()
    {
        return $this->get('infinite_scroll', false);
    }

    /**
     * {@inheritdoc}
     */
    public function lastPage()
    {
        return $this->pattern->viewer(
            'pagination-last',
            [
                'disabled'   => $this->isDisableLast(),
                'url'        => $this->pagedUrl($this->getTotalPages()),
                'pagination' => $this,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function nextPage()
    {
        return $this->pattern->viewer(
            'pagination-next',
            [
                'disabled'   => $this->isDisableNext(),
                'url'        => $this->pagedUrl(min($this->getTotalPages(), $this->pageNum() + 1)),
                'pagination' => $this,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function pageNum()
    {
        return intval($this->pattern->request()->getPageNum());
    }

    /**
     * {@inheritdoc}
     */
    public function pagedUrl($page)
    {
        return $this->pattern->url()->with(['paged' => $page], $this->unpagedUrl());
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->getTotalPages() && $this->getPerPage() > 0) :
            $this->set('total_pages', ceil($this->getTotalItems() / $this->getPerPage()));
        endif;

        if (! headers_sent() &&
            ! wp_doing_ajax() &&
            $this->getTotalPages() > 0 &&
            $this->pageNum() > $this->getTotalPages()
        ) :
            wp_redirect(add_query_arg('paged', $this->getTotalPages()));
            exit;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function prevPage()
    {
        return $this->pattern->viewer(
            'pagination-prev',
            [
                'disabled'   => $this->isDisablePrev(),
                'url'        => $this->pagedUrl(max(1,  $this->pageNum() - 1)),
                'pagination' => $this,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unpagedUrl()
    {
        return $this->pattern->url()->without(['paged']);
    }

    /**
     * {@inheritdoc}
     */
    public function which($which)
    {
        $this->which = $which;

        return $this;
    }
}