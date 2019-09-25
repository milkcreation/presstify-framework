<?php

namespace tiFy\Metabox;

use tiFy\Container\ServiceProvider;
use tiFy\Metabox\Tab\MetaboxTabController;

class MetaboxServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet un chargement différé des services.}
     * @var string[]
     */
    protected $provides = [
        'metabox',
        'metabox.viewer'
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->getContainer()->share('metabox', function () {
            return new MetaboxManager($this->getContainer());
        });
    }
}