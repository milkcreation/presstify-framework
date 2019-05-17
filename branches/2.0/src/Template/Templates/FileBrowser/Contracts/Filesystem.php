<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileBrowser\Contracts;

use tiFy\Contracts\Filesystem\Filesystem as tiFyFilesystem;
use tiFy\Contracts\Template\FactoryAwareTrait;

interface Filesystem extends tiFyFilesystem, FactoryAwareTrait
{
    /**
     * Définition d'une instance de la classe
     *
     * @return static
     */
    public static function createFromFactory(FileBrowser $factory): Filesystem;
}