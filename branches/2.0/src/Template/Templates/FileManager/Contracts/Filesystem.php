<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager\Contracts;

use tiFy\Contracts\Filesystem\Filesystem as tiFyFilesystem;
use tiFy\Contracts\Template\FactoryAwareTrait;

interface Filesystem extends tiFyFilesystem, FactoryAwareTrait
{
    /**
     * Définition d'une instance de la classe.
     *
     * @param Factory $factory
     *
     * @return static
     */
    public static function createFromFactory(Factory $factory): Filesystem;
}