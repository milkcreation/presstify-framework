<?php

namespace tiFy\PostType;

use tiFy\App\Container\AppServiceProvider;
use tiFy\PostType\Metadata\Post as MetadataPost;

class PostTypeServiceProvider extends AppServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'post_type',
        'post_type.factory',
        'post_type.labels'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(
            MetadataPost::class
        )->build();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerManager();
        $this->registerFactory();
        $this->registerLabels();
    }

    /**
     * Déclaration du controleur de gestion des types de contenu.
     *
     * @return void
     */
    public function registerManager()
    {
        $this->app->share('post_type', PostType::class);
    }

    /**
     * Déclaration du controleur de déclaration d'un type de contenu.
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->app->add('post_type.factory', PostTypeFactory::class);
    }

    /**
     * Déclaration du controleur de déclaration des intitulés de type de contenu.
     *
     * @return void
     */
    public function registerLabels()
    {
        $this->app->add('post_type.labels', PostTypeLabels::class);
    }
}