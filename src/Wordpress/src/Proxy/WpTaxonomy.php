<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Proxy;

use Pollen\Proxy\AbstractProxy;
use Pollen\WpTerm\WpTaxonomyManagerInterface;

/**
 * @method static
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