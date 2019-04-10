<?php declare(strict_types=1);

namespace tiFy\Taxonomy;

use InvalidArgumentException;
use tiFy\Contracts\Taxonomy\TaxonomyFactory as TaxonomyFactoryContract;
use tiFy\Contracts\Taxonomy\TaxonomyManager as TaxonomyManagerContract;
use tiFy\Contracts\Taxonomy\TaxonomyTermMeta;
use tiFy\Support\Manager;

class TaxonomyManager extends Manager implements TaxonomyManagerContract
{
    /**
     * @inheritdoc
     */
    public function get($name): ?TaxonomyFactoryContract
    {
        return parent::get($name);
    }

    /**
     * @inheritdoc
     */
    public function register($name, array $attrs = []): TaxonomyManagerContract
    {
        return $this->set([$name => new TaxonomyFactory($name, $attrs)]);
    }

    /**
     * @inheritdoc
     */
    public function term_meta(): ?TaxonomyTermMeta
    {
        return $this->resolve('term-meta');
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $alias)
    {
        return $this->container->get("taxonomy.{$alias}");
    }

    /**
     * @inheritdoc
     */
    public function walk(&$item, $key = null)
    {
        if (!$item instanceof TaxonomyFactoryContract) {
            throw new InvalidArgumentException(sprintf(
                __('La taxonomie devrait être une instance de %s'),
                TaxonomyFactoryContract::class
            ));
        } else {
            $item->setManager($this)->boot();
        }
    }
}