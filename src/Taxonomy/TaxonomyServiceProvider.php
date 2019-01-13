<?php

namespace tiFy\Taxonomy;

use tiFy\App\Container\AppServiceProvider;

class TaxonomyServiceProvider extends AppServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'taxonomy',
        'taxonomy.factory',
        'taxonomy.term.meta'
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerManager();
        $this->registerFactory();
        $this->registerTermMeta();
    }

    /**
     * Déclaration du controleur principal.
     *
     * @return void
     */
    public function registerManager()
    {
        $this->getContainer()->share('taxonomy', TaxonomyManager::class);
    }

    /**
     * Déclaration du controleur de gestion d'une taxonomie.
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->getContainer()->add('taxonomy.factory', TaxonomyFactory::class);
    }

    /**
     * Déclaration du controleur de gestion des metadonnées de terme d'une taxonomie.
     *
     * @return void
     */
    public function registerTermMeta()
    {
        $this->getContainer()->share('taxonomy.term.meta', TaxonomyTermMeta::class);
    }
}