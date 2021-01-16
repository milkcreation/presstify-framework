<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\Drivers\Breadcrumb\BreadcrumbCollection;
use tiFy\Partial\Drivers\Breadcrumb\BreadcrumbCollectionInterface;
use tiFy\Partial\PartialDriver;

class BreadcrumbDriver extends PartialDriver implements BreadcrumbDriverInterface
{
    /**
     * Instance principale.
     * @var BreadcrumbDriverInterface|null
     */
    protected static $main;

    /**
     * Indicateur de d'activation d'affichage du fil d'ariane.
     * @var bool
     */
    private $enabled = true;

    /**
     * Instance du gestionnaire de collection d'éléments.
     * @var BreadcrumbCollectionInterface
     */
    protected $collection;

    /**
     * @inheritDoc
     */
    public function add($item): ?int
    {
        if ($item = $this->parseItem($item)) {
            return $this->collection()->add(...$item);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function append($item): ?int
    {
        return $this->add($item);
    }

    /**
     * @inheritDoc
     */
    public function collection(): BreadcrumbCollectionInterface
    {
        if (is_null($this->collection)) {
            $this->collection = new BreadcrumbCollection($this);
        }

        return $this->collection;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string[]|array[]|object[]|callable[] $items Liste des élements du fil d'ariane.
             */
            'items'  => [],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function disable(): BreadcrumbDriverInterface
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function enable(): BreadcrumbDriverInterface
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * Récupération de la liste des éléments à afficher.
     *
     * @return static
     */
    protected function fetch(): BreadcrumbDriverInterface
    {
        $this->set('parts', $this->isEnabled() ? $this->collection()->prefetch()->fetch() : []);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function flush(): BreadcrumbDriverInterface
    {
        $this->collection()->clear();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function main(): BreadcrumbDriverInterface
    {
        if (is_null(static::$main)) {
            static::$main = $this;
        }

        return static::$main;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return !!$this->enabled;
    }

    /**
     * @inheritDoc
     */
    public function insert(int $position, $item): ?int
    {
        if ($item = $this->parseItem($item)) {
            $item[1] = $position;

            return $this->collection()->add(...$item);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function move(int $from, int $to): ?int
    {
        return $this->collection()->move($from, $to);
    }

    /**
     * @inheritDoc
     */
    public function parseItem($item): ?array
    {
        if (is_string($item)) {
            return [$item, null, []];
        } elseif (is_object($item)) {
            return [(string)$item, null, []];
        } elseif (is_array($item)) {
            return [
                $this->collection()->getRender(
                    $item['content'] ?? '', $item['url'] ?? null, $item['attrs'] ?? []
                ),
                $item['position'] ?? null,
                $item['wrapper'] ?? [],
            ];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function prefetch(): BreadcrumbDriverInterface
    {
        $this->collection()->prefetch();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prepend($item): ?int
    {
        return $this->insert(0, $item);
    }

    /**
     * @inheritDoc
     */
    public function remove(int $position): BreadcrumbDriverInterface
    {
        $this->collection()->clear($position);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $obj = !is_null(static::$main) ? static::$main : $this;

        return $obj->fetch()->view('index', $obj->all());
    }

    /**
     * @inheritDoc
     */
    public function replace(int $position, $item): ?int
    {
        if ($item = $this->parseItem($item)) {
            $item[1] = $position;

            return $this->collection()->clear($position)->add(...$item);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->partialManager()->resources("/views/breadcrumb");
    }
}