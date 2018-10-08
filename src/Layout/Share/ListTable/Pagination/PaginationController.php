<?php

namespace tiFy\Layout\Share\ListTable\Pagination;

use tiFy\Kernel\Parameters\AbstractParametersBagIterator;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;
use tiFy\Layout\Share\ListTable\Contracts\PaginationInterface;

class PaginationController extends AbstractParametersBagIterator implements PaginationInterface
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
     * Instance de la disposition associée.
     * @var ListTableInterface
     */
    protected $layout;

    /**
     * CONSTRUCTEUR.
     *
     * @param ListTableInterface $layout Instance de la disposition associée.
     *
     * @return void
     */
    public function __construct(ListTableInterface $layout)
    {
        $this->layout = $layout;

        parent::__construct([]);
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
    public function getPageLinks($which)
    {
        $current_url = $this->layout->request()->sanitizeUrl();
        $page_num = $this->layout->request()->getPageNum();
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

        if ('bottom' === $which) :
            $html_current_page = $this->layout->request()->getPagenum();
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

        return $page_links;
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
        if ($this->layout->request()->getPagenum() === 1 || $this->layout->request()->getPagenum() === 2) :
            return true;
        endif;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisableLast()
    {
        if ($this->layout->request()->getPagenum() >= $this->getTotalPages() - 1) :
            return true;
        endif;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisableNext()
    {
        if ($this->layout->request()->getPagenum() === $this->getTotalPages()) :
            return true;
        endif;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisablePrev()
    {
        if ($this->layout->request()->getPagenum() === 1) :
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

        if (!headers_sent() && !wp_doing_ajax() && $this->getTotalPages() > 0 && $this->layout->request()->getPagenum() > $this->getTotalPages()) :
            wp_redirect(add_query_arg('paged', $this->getTotalPages()));
            exit;
        endif;
    }
}