<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Proxy;

use Pollen\Proxy\AbstractProxy;
use Pollen\WpTaxonomy\WpTaxonomyManagerInterface;
use Pollen\WpTaxonomy\WpTermQueryInterface;
use WP_Term;
use WP_Term_Query;

/**
 * @method static WpTermQueryInterface|null term(string|int|WP_Term|null $term = null)
 * @method static WpTermQueryInterface[]|array terms(WP_Term_Query|array|null $query = null)
 */
class WpTaxonomy extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return WpTaxonomyManagerInterface
     */
    public static function getInstance(): WpTaxonomyManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return WpTaxonomyManagerInterface::class;
    }
}