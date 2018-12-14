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
        'per_page'    => 0
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
    public function __toString()
    {
        return (string)$this->display();
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        $current_url = $this->pattern->request()->sanitizeUrl();
        $page_num = $this->pattern->request()->getPageNum();
        $total_pages_before = '<span class="paging-input">';
        $total_pages_after = '</span></span>';
        $page_links = [];

        if ($this->isDisableFirst()) :
            $page_links[] = partial(
                'tag',
                [
                    'tag'     => 'span',
                    'attrs'   => [
                        'class'       => 'tablenav-pages-navspan',
                        'aria-hidden' => 'true',
                    ],
                    'content' => '&laquo;',
                ]
            );
        else :
            $page_links[] = sprintf("<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url(remove_query_arg('paged', $current_url)),
                __('Première page', 'tify'),
                '&laquo;'
            );
        endif;

        if ($this->isDisablePrev()) :
            $page_links[] = partial(
                'tag',
                [
                    'tag'     => 'span',
                    'attrs'   => [
                        'class'       => 'tablenav-pages-navspan',
                        'aria-hidden' => 'true',
                    ],
                    'content' => '&lsaquo;',
                ]
            );
        else :
            $page_links[] = sprintf("<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url(add_query_arg('paged', max(1, $page_num - 1), $current_url)),
                __('Page précédente', 'tify'),
                '&lsaquo;'
            );
        endif;

        if ('bottom' === $this->which) :
            $html_current_page = $this->pattern->request()->getPagenum();
            $total_pages_before = '<span class="screen-reader-text">' . __('Page courante', 'tify') . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        else :
            $html_current_page = sprintf("%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector" class="screen-reader-text">' . __('Page courante', 'tify') . '</label>',
                $page_num,
                strlen($this->getTotalPages())
            );
        endif;

        $html_total_pages = sprintf("<span class='total-pages'>%s</span>", number_format_i18n($this->getTotalPages()));
        $page_links[] = $total_pages_before .
            sprintf(
                _x('%1$s of %2$s', 'paging'),
                $html_current_page,
                $this->getTotalPages()
            ) .
            $total_pages_after;

        if ($this->isDisableNext()) :
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
        else :
            $page_links[] = sprintf("<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url(add_query_arg('paged', min($this->getTotalPages(), $page_num + 1), $current_url)),
                __('Page suivante', 'tify'),
                '&rsaquo;'
            );
        endif;

        if ($this->isDisableLast()) :
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
        else :
            $page_links[] = sprintf("<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url(add_query_arg('paged', $this->getTotalPages(), $current_url)),
                __('Dernière page', 'tify'),
                '&raquo;'
            );
        endif;

        return join("\n", $page_links);
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
        return $this->get('per_page', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalItems()
    {
        return $this->get('total_items', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalPages()
    {
        return $this->get('total_pages', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function isDisableFirst()
    {
        if ($this->pattern->request()->getPagenum() === 1 || $this->pattern->request()->getPagenum() === 2) :
            return true;
        endif;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisableLast()
    {
        if ($this->pattern->request()->getPagenum() >= $this->getTotalPages() - 1) :
            return true;
        endif;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisableNext()
    {
        if ($this->pattern->request()->getPagenum() === $this->getTotalPages()) :
            return true;
        endif;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisablePrev()
    {
        if ($this->pattern->request()->getPagenum() === 1) :
            return true;
        endif;

        return false;
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
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->getTotalPages() && $this->getPerPage() > 0) :
            $this->set('total_pages', ceil($this->getTotalItems() / $this->getPerPage()));
        endif;

        if (!headers_sent() &&
            !wp_doing_ajax() &&
            $this->getTotalPages() > 0 &&
            $this->pattern->request()->getPagenum() > $this->getTotalPages()
        ) :
            wp_redirect(add_query_arg('paged', $this->getTotalPages()));
            exit;
        endif;
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