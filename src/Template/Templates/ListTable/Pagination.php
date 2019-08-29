<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Support\ParamsBag;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Contracts\{ListTable, Pagination as PaginationContract};

class Pagination extends ParamsBag implements PaginationContract
{
    use FactoryAwareTrait;

    /**
     * Nombre d'élément de la page courante.
     * @var int
     */
    protected $count = 0;

    /**
     * Numéro de la page courante.
     * @var int
     */
    protected $current_page = 0;

    /**
     * Numéro de la dernière page.
     * @var int
     */
    protected $last_page = 0;

    /**
     * Nombre d'élément par page.
     * @var int
     */
    protected $per_page = 0;

    /**
     * Nombre total d'éléments.
     * @var int
     */
    protected $total = 0;

    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Emplacement d'affichage.
     * @return string top|bottom.
     */
    protected $which = 'top';

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'attrs'        => [],
            'first'        => [],
            'last'         => [],
            'next'         => [],
            'prev'         => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPage(): int
    {
        return $this->current_page;
    }

    /**
     * @inheritDoc
     */
    public function getLastPage(): int
    {
        return $this->last_page;
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): int
    {
        return $this->per_page;
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function getWhich(): string
    {
        return $this->which;
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

        return $this
            ->parseFirst()
            ->parseLast()
            ->parseNext()
            ->parsePrev();
    }

    /**
     * @inheritDoc
     */
    public function parseFirst(): PaginationContract
    {
        $class = $this->isDisableFirst() ? 'tablenav-pages-navspan button disabled' : 'first-page button';
        if (!$this->has('first.attrs.class')) {
            $this->set('first.attrs.class', $class);
        } elseif ($_class = $this->get('first.attrs.class')) {
            $this->set('first.attrs.class', sprintf($_class, $class));
        }

        if ($this->isDisableFirst()) {
            if (!$this->has('first.tag')) {
                $this->set('first.tag', 'span');
            }

            $this->set('first.attrs.aria-hidden', 'true');
        } else {
            if (!$this->has('first.tag')) {
                $this->set('first.tag', 'a');
            }
            if (!$this->has('first.attrs.href')) {
                $this->set('first.attrs.href', $this->unpagedUrl());
            }
        }

        if (!$this->has('first.content')) {
            $this->set('first.content', '&laquo;');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseLast(): PaginationContract
    {
        $class = $this->isDisableLast() ? 'tablenav-pages-navspan button disabled' : 'last-page button';
        if (!$this->has('last.attrs.class')) {
            $this->set('last.attrs.class', $class);
        } elseif ($_class = $this->get('last.attrs.class')) {
            $this->set('last.attrs.class', sprintf($_class, $class));
        }

        if ($this->isDisableLast()) {
            if (!$this->has('last.tag')) {
                $this->set('last.tag', 'span');
            }

            $this->set('last.attrs.aria-hidden', 'true');
        } else {
            if (!$this->has('last.tag')) {
                $this->set('last.tag', 'a');
            }
            if (!$this->has('last.attrs.href')) {
                $this->set('last.attrs.href', $this->pagedUrl($this->getLastPage()));
            }
        }

        if (!$this->has('last.content')) {
            $this->set('last.content', '&raquo;');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseNext(): PaginationContract
    {
        $class = $this->isDisableNext() ? 'tablenav-pages-navspan button disabled' : 'next-page button';
        if (!$this->has('next.attrs.class')) {
            $this->set('next.attrs.class', $class);
        } elseif ($_class = $this->get('next.attrs.class')) {
            $this->set('next.attrs.class', sprintf($_class, $class));
        }

        if ($this->isDisableNext()) {
            if (!$this->has('next.tag')) {
                $this->set('next.tag', 'span');
            }

            $this->set('next.attrs.aria-hidden', 'true');
        } else {
            if (!$this->has('next.tag')) {
                $this->set('next.tag', 'a');
            }
            if (!$this->has('next.attrs.href')) {
                $this->set('next.attrs.href', $this->pagedUrl(min($this->getLastPage(), $this->getCurrentPage() + 1)));
            }
        }

        if (!$this->has('next.content')) {
            $this->set('next.content', '&rsaquo;');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parsePrev(): PaginationContract
    {
        $class = $this->isDisablePrev() ? 'tablenav-pages-navspan button disabled' : 'prev-page button';
        if (!$this->has('prev.attrs.class')) {
            $this->set('prev.attrs.class', $class);
        } elseif ($_class = $this->get('prev.attrs.class')) {
            $this->set('prev.attrs.class', sprintf($_class, $class));
        }

        if ($this->isDisablePrev()) {
            if (!$this->has('prev.tag')) {
                $this->set('prev.tag', 'span');
            }
            $this->set('prev.attrs.aria-hidden', 'true');
        } else {
            if (!$this->has('prev.tag')) {
                $this->set('prev.tag', 'a');
            }
            if (!$this->has('prev.attrs.href')) {
                $this->set('prev.attrs.href', $this->pagedUrl(max(1, $this->getCurrentPage() - 1)));
            }
        }

        if (!$this->has('prev.content')) {
            $this->set('prev.content', '&lsaquo;');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCount(int $count): PaginationContract
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCurrentPage(int $num): PaginationContract
    {
        $this->current_page = $num;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLastPage(int $num): PaginationContract
    {
        $this->last_page = $num;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPerPage(int $per_page): PaginationContract
    {
        $this->per_page = $per_page;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTotal(int $total): PaginationContract
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setWhich(string $which): PaginationContract
    {
        $this->which = $which;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unpagedUrl(): string
    {
        return $this->factory->url()->without(['paged']);
    }
}