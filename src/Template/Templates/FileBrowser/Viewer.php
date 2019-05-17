<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileBrowser;

use tiFy\Template\Factory\FactoryViewer;
use tiFy\Template\Templates\FileBrowser\Contracts\{Breadcrumb, FileCollection, FileInfo};

/**
 * Class Viewer
 * @package tiFy\Template\Templates\FileBrowser
 *
 * @method Breadcrumb|iterable breadcrumb()
 * @method FileInfo|null getFile(?string $path = null)
 * @method FileCollection|FileInfo[] getFiles(?string $path = null, bool $recursive = false)
 */
class Viewer extends FactoryViewer
{
    /**
     * Instance du gabarit associé.
     * @var FileBrowser
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function boot()
    {
        parent::boot();

        array_push(
            $this->mixins,
            'breadcrumb',
            'getFile',
            'getFiles',
            'param'
        );
    }
}