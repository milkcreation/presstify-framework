<?php

namespace tiFy\PostType;

use tiFy\App\Container\AppServiceProvider;

class PostTypeServiceProvider extends AppServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'post-type',
        'post-type.factory',
        'post-type.post.meta'
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerManager();
        $this->registerFactory();
        $this->registerPostMeta();
    }

    /**
     * Déclaration du controleur de gestion des types de contenu.
     *
     * @return void
     */
    public function registerManager()
    {
        $this->getContainer()->share('post-type', PostTypeManager::class);
    }

    /**
     * Déclaration du controleur de déclaration d'un type de contenu.
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->getContainer()->add('post-type.factory', PostTypeFactory::class);
    }

    /**
     * Déclaration du controleur de gestion des metadonnées de post.
     *
     * @return void
     */
    public function registerPostMeta()
    {
        $this->getContainer()->share('post-type.post.meta', PostTypePostMeta::class);
    }
}