<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager;

use tiFy\Template\Templates\FileManager\Contracts\Factory;
use tiFy\Template\Factory\Cache as BaseCache;

class Cache extends BaseCache
{
    /**
     * Instance du gabarit associé.
     * @var Factory
     */
    protected $factory;
}