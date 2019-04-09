<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Pagination;

use tiFy\Support\ParamsBag;
use tiFy\Template\Templates\ListTable\Contracts\Pagination as PaginationContract;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class Pagination extends ParamsBag implements PaginationContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     *
     */
    protected $which;

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
    }

    /**
     * @inheritdoc
     */
    public function currentPage(): string
    {
        $total_pages_before = '<span class="paging-input">';
        $total_pages_after = '</span></span>';

        if ('bottom' === $this->which) {
            $html_current_page = (string)$this->pageNum();
            $total_pages_before = '<span class="screen-reader-text">' .
                __('Page courante', 'tify') .
                '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        } else {
            $html_current_page = sprintf(
                "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s'" .
                " size='%s' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector" class="screen-reader-text">' .
                __('Page courante', 'tify') .
                '</label>',
                (string)$this->pageNum(),
                strlen((string)$this->getTotalPages())
            );
        }

        /*$html_total_pages = sprintf(
            "<span class='total-pages'>%s</span>", number_format_i18n($this->getTotalPages())
        );*/

        return $total_pages_before . sprintf(
            _x('%1$s sur %2$s', 'paging', 'tify'),
            $html_current_page,
            $this->getTotalPages()
        ) . $total_pages_after;
    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'attrs'       => [],
            'per_page'    => 0,
            'total_items' => 0,
            'total_pages' => 0
        ];
    }

    /**
     * @inheritdoc
     */
    public function firstPage(): string
    {
        return (string)$this->factory->viewer('pagination-first', [
            'disabled'   => $this->isDisableFirst(),
            'url'        => $this->unpagedUrl(),
            'pagination' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getPerPage(): int
    {
        return intval($this->get('per_page', 0));
    }

    /**
     * @inheritdoc
     */
    public function getTotalItems(): int
    {
        return intval($this->get('total_items', 0));
    }

    /**
     * @inheritdoc
     */
    public function getTotalPages(): int
    {
        return intval($this->get('total_pages', 0));
    }

    /**
     * @inheritdoc
     */
    public function isDisableFirst(): bool
    {
        return ($this->pageNum() === 1 || $this->factory->request()->getPagenum() === 2);
    }

    /**
     * @inheritdoc
     */
    public function isDisableLast(): bool
    {
        return $this->pageNum() >= ($this->getTotalPages() - 1);
    }

    /**
     * @inheritdoc
     */
    public function isDisableNext(): bool
    {
        return $this->pageNum() === $this->getTotalPages();
    }

    /**
     * @inheritdoc
     */
    public function isDisablePrev(): bool
    {
        return $this->pageNum() === 1;
    }

    /**
     * @inheritdoc
     */
    public function isInfiniteScroll(): bool
    {
        return !!$this->get('infinite_scroll', false);
    }

    /**
     * @inheritdoc
     */
    public function lastPage(): string
    {
        return (string)$this->factory->viewer('pagination-last', [
            'disabled'   => $this->isDisableLast(),
            'url'        => $this->pagedUrl($this->getTotalPages()),
            'pagination' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function nextPage(): string
    {
        return (string)$this->factory->viewer('pagination-next', [
            'disabled'   => $this->isDisableNext(),
            'url'        => $this->pagedUrl(min($this->getTotalPages(), $this->pageNum() + 1)),
            'pagination' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function pageNum(): int
    {
        return intval($this->factory->request()->getPageNum());
    }

    /**
     * @inheritdoc
     */
    public function pagedUrl(int $page): string
    {
        return $this->factory->url()->with(['paged' => $page]);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        if (!$this->getTotalPages() && $this->getPerPage() > 0) {
            $this->set('total_pages', ceil($this->getTotalItems() / $this->getPerPage()));
        }

        if (! headers_sent() &&
            ! wp_doing_ajax() &&
            $this->getTotalPages() > 0 &&
            $this->pageNum() > $this->getTotalPages()
        ) {
            wp_redirect(add_query_arg('paged', $this->getTotalPages()));
            exit;
        }

        $classes = [];
        $classes[] = 'tablenav-pages';
        $classes[] = ($total_pages = $this->getTotalPages())
            ? ($total_pages < 2 ? 'one-page' : '')
            : 'no-pages';

        if ($class = $this->get('attrs.class')) {
            $this->set('attrs.class', sprintf($class, join(' ', $classes)));
        } else {
            $this->set('attrs.class', join(' ', $classes));
        }

        if ($this->factory->ajax()) {
            $this->set('attrs.data-control', 'list-table.pagination');
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function prevPage(): string
    {
        return (string)$this->factory->viewer('pagination-prev', [
            'disabled'   => $this->isDisablePrev(),
            'url'        => $this->pagedUrl(max(1,  $this->pageNum() - 1)),
            'pagination' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unpagedUrl(): string
    {
        return $this->factory->url()->without(['paged']);
    }

    /**
     * @inheritdoc
     */
    public function which(string $which): PaginationContract
    {
        $this->which = $which;

        return $this;
    }
}