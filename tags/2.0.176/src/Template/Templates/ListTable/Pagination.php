<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Support\ParamsBag;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Contracts\{ListTable, Pagination as PaginationContract};

class Pagination extends ParamsBag implements PaginationContract
{
    use FactoryAwareTrait;

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
     * @inheritDoc
     */
    public function currentPage(): string
    {
        $total_pages_before = '<span class="paging-input">';
        $total_pages_after = '</span></span>';

        if ('bottom' === $this->which) {
            $html_current_page = (string)$this->getCurrentPage();
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
                (string)$this->getCurrentPage(),
                strlen((string)$this->getLastPage())
            );
        }

        /*$html_total_pages = sprintf(
            "<span class='total-pages'>%s</span>", number_format_i18n($this->getTotalPaged())
        );*/

        return $total_pages_before . sprintf(
                _x('%1$s sur %2$s', 'paging', 'tify'),
                $html_current_page,
                $this->getLastPage()
            ) . $total_pages_after;
    }

    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'attrs'        => [],
            'count'        => 0,
            'current_page' => 0,
            'last_page'    => 0,
            'per_page'     => 0,
            'total'        => 0,
        ];
    }

    /**
     * @inheritDoc
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
     * Récupération du numéro de la page courante.
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return (int)$this->get('current_page', 0);
    }

    /**
     * @inheritDoc
     */
    public function getLastPage(): int
    {
        return (int)$this->get('last_page', 0);
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): int
    {
        return (int)$this->get('per_page', 0);
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): int
    {
        return (int)$this->get('total', 0);
    }

    /**
     * @inheritDoc
     */
    public function isDisableFirst(): bool
    {
        return ($this->getCurrentPage() === 1 || $this->getCurrentPage() === 2);
    }

    /**
     * @inheritDoc
     */
    public function isDisableLast(): bool
    {
        return $this->getCurrentPage() >= ($this->getLastPage() - 1);
    }

    /**
     * @inheritDoc
     */
    public function isDisableNext(): bool
    {
        return $this->getCurrentPage() === $this->getLastPage();
    }

    /**
     * @inheritDoc
     */
    public function isDisablePrev(): bool
    {
        return $this->getCurrentPage() === 1;
    }

    /**
     * @inheritDoc
     */
    public function isInfiniteScroll(): bool
    {
        return !!$this->get('infinite_scroll', false);
    }

    /**
     * @inheritDoc
     */
    public function lastPage(): string
    {
        return (string)$this->factory->viewer('pagination-last', [
            'disabled'   => $this->isDisableLast(),
            'url'        => $this->pagedUrl($this->getLastPage()),
            'pagination' => $this,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function nextPage(): string
    {
        return (string)$this->factory->viewer('pagination-next', [
            'disabled'   => $this->isDisableNext(),
            'url'        => $this->pagedUrl(min($this->getLastPage(), $this->getCurrentPage() + 1)),
            'pagination' => $this,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function pagedUrl(int $page): string
    {
        return $this->factory->url()->with(['paged' => $page]);
    }

    /**
     * @inheritDoc
     */
    public function parse(): PaginationContract
    {
        parent::parse();

        $classes = [];
        $classes[] = 'tablenav-pages';
        $classes[] = ($total_pages = $this->getLastPage())
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
     * @inheritDoc
     */
    public function prevPage(): string
    {
        return (string)$this->factory->viewer('pagination-prev', [
            'disabled'   => $this->isDisablePrev(),
            'url'        => $this->pagedUrl(max(1, $this->getCurrentPage() - 1)),
            'pagination' => $this,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unpagedUrl(): string
    {
        return $this->factory->url()->without(['paged']);
    }

    /**
     * @inheritDoc
     */
    public function which(string $which): PaginationContract
    {
        $this->which = $which;

        return $this;
    }
}