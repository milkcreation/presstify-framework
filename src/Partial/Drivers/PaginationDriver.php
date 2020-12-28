<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\Drivers\Pagination\PaginationQuery;
use tiFy\Partial\Drivers\Pagination\PaginationQueryInterface;
use tiFy\Partial\Drivers\Pagination\PaginationView;
use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;

class PaginationDriver extends PartialDriver implements PaginationDriverInterface
{
    /**
     * Instance du gestionnaire de requête de récupération des éléments.
     * @var PaginationQueryInterface|null
     */
    protected $query;

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string|array|null $url Url de pagination {
             * @var string|null $base l'url peux contenir %d en remplacement du numéro de page. Si null, url courante.
             * @var bool $segment Activation de la réécriture depuis un segment de l'url|depuis des arguments de requête.
             * @var string $index Indice de qualification d'une page
             * }
             */
            'url'   => null,
            /**
             * @var array $links {
             * @var bool|array $first Activation du lien vers la première page|Liste d'attributs.
             * @var bool|array $last Activation du lien vers la dernière page|Liste d'attributs.
             * @var bool|array $previous Activation du lien vers la page précédente|Liste d'attributs.
             * @var bool|array $next Activation du lien vers la page suivante|Liste d'attributs.
             * @var bool|array $numbers Activation de l'affichage de la numérotation des pages|Liste d'attributs {
             * @var int $range
             * @var int $anchor
             * @var int $gap
             * }
             */
            'links' => [
                'first'    => true,
                'last'     => true,
                'previous' => true,
                'next'     => true,
                'numbers'  => true,
            ],
            /**
             * @var array|PaginationQueryInterface|object $query
             */
            'query' => null,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function ellipsis(array &$numbers): void
    {
        $numbers[] = [
            'tag'     => 'span',
            'content' => '...',
            'attrs'   => 'Pagination-itemEllipsis',
        ];
    }

    /**
     * @inheritDoc
     */
    public function numLoop(array &$numbers, int $start, int $end): void
    {
        for ($num = $start; $num <= $end; $num++) {
            if ($num === 1 && !$this->query->getCurrentPage()) {
                $current = 'true';
            } elseif ($this->query->getCurrentPage() === $num) {
                $current = 'true';
            } else {
                $current = 'false';
            }

            $numbers[] = [
                'tag'     => 'a',
                'content' => $num,
                'attrs'   => [
                    'class'        => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'         => $this->query->getPageNumUrl($num),
                    'aria-current' => $current,
                ],
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        $this->parseQuery()->parseUrl();

        $this->query->parse();

        $this->parseLinks();

        if ($this->get('links.numbers')) {
            $this->parseNumbers();
        }

        $this->view()->setFactory(PaginationView::class);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseLinks(): PaginationDriverInterface
    {
        $defaults = [
            'first'    => [
                'tag'     => 'a',
                'content' => '&laquo;',
                'attrs'   => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'  => $this->query->getPageNumUrl(1),
                ],
            ],
            'last'     => [
                'tag'     => 'a',
                'content' => '&raquo;',
                'attrs'   => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'  => $this->query->getPageNumUrl($this->query->getLastPage()),
                ],
            ],
            'previous' => [
                'tag'     => 'a',
                'content' => '&lsaquo;',
                'attrs'   => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'  => $this->query->getPageNumUrl($this->query->getCurrentPage() - 1),
                ],
            ],
            'next'     => [
                'tag'     => 'a',
                'content' => '&rsaquo;',
                'attrs'   => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href'  => $this->query->getPageNumUrl($this->query->getCurrentPage() + 1),
                ],
            ],
        ];

        foreach ($defaults as $link => $default) {
            $attrs = $this->get("links.{$link}", []);

            if ($attrs === false) {
                $attrs = [];
            } elseif ($attrs === true) {
                $attrs = $default;
            } elseif (is_string($attrs)) {
                $attrs = array_merge($default, ['content' => $attrs]);
            } else {
                $attrs = array_merge($default, $attrs);
            }

            $this->set("links.{$link}", $attrs);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseNumbers(): PaginationDriverInterface
    {
        $range = intval($this->get('links.numbers.range', 2));
        $anchor = intval($this->get('links.numbers.anchor', 3));
        $gap = intval($this->get('links.numbers.gap', 1));

        $min_links = ($range * 2) + 1;
        $block_min = min($this->query->getCurrentPage() - $range, $this->query->getLastPage() - $min_links);
        $block_high = max($this->query->getCurrentPage() + $range, $min_links);

        $left_gap = ($block_min - $anchor - $gap) > 0;
        $right_gap = ($block_high + $anchor + $gap) < $this->query->getLastPage();

        $numbers = [];
        if ($left_gap && !$right_gap) {
            $this->numLoop($numbers, 1, $anchor);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, $block_min, $this->query->getLastPage());
        } elseif ($left_gap && $right_gap) {
            $this->numLoop($numbers, 1, $anchor);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, $block_min, $block_high);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, ($this->query->getLastPage() - $anchor + 1), $this->query->getLastPage());
        } elseif (!$left_gap && $right_gap) {
            $this->numLoop($numbers, 1, $block_high);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, ($this->query->getLastPage() - $anchor + 1), $this->query->getLastPage());
        } else {
            $this->numLoop($numbers, 1, $this->query->getLastPage());
        }

        $this->set('numbers', $numbers);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseQuery(): PaginationDriverInterface
    {
        $this->query = $this->pull('query', []);
        if (!$this->query instanceof PaginationQueryInterface) {
            $this->query = (new PaginationQuery($this->query));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseUrl(): PaginationDriverInterface
    {
        if ($this->has('url.base')) {
            $this->query->setBaseUrl($this->get('url.base'));
        }

        if ($this->has('url.segment')) {
            $this->query->setSegmentUrl($this->get('url.segment'));
        }

        if ($this->has('url.index')) {
            $this->query->setPageIndex($this->get('url.index'));
        }

        if (!is_array($this->get('url'))) {
            $this->query->setBaseUrl($this->get('url', null));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function query(): ?PaginationQueryInterface
    {
        return $this->query;
    }
}