<?php

namespace tiFy\Kernel;

use App\App;

use tiFy\AdminView\AdminView;
use tiFy\AjaxAction\AjaxAction;
use tiFy\Api\ApiServiceProvider;
use tiFy\Column\Column;
use tiFy\Cron\Cron;
use tiFy\Db\Db;
use tiFy\Field\Field;
use tiFy\Form\Form;
use tiFy\Media\Media;
use tiFy\Metabox\Metabox;
use tiFy\Metadata\Metadata;
use tiFy\MetaTag\MetaTag;
use tiFy\Options\Options;
use tiFy\PageHook\PageHook;
use tiFy\Partial\Partial;
use tiFy\PostType\PostType;
use tiFy\Route\Route;
use tiFy\TabMetabox\TabMetabox;
use tiFy\Taxonomy\Taxonomy;
use tiFy\User\User;
use tiFy\View\View;

use tiFy\Kernel\Assets\Assets;
use tiFy\Kernel\Assets\AssetsInterface;
use tiFy\Kernel\ClassInfo\ClassInfo;
use tiFy\Kernel\Composer\ClassLoader;
use tiFy\Kernel\Events\Events;
use tiFy\Kernel\Events\EventsInterface;
use tiFy\Kernel\Http\Request;
use tiFy\Kernel\Logger\Logger;
use tiFy\Kernel\Templates\Engine;
use tiFy\Kernel\Templates\EngineInterface;
use tiFy\Kernel\Service;

use tiFy\Kernel\Container\ServiceProvider;

class KernelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $singletons = [
        App::class,
        Assets::class,
        Partial::class
    ];

    /**
     * {@inheritdoc}
     */
    protected $bindings = [
        ClassInfo::class,
        Engine::class,
        Events::class
    ];

    /**
     * {@inheritdoc}
     */
    protected $aliases = [
        AssetsInterface::class => Assets::class,
        EngineInterface::class => Engine::class,
        EventsInterface::class => Events::class
    ];

    /**
     * Liste des packages natifs (composants)
     * @return array
     */
    protected $components = [
        AdminView::class,
        AjaxAction::class,
        Column::class,
        Cron::class,
        Db::class,
        Field::class,
        Form::class,
        Media::class,
        Metabox::class,
        Metadata::class,
        MetaTag::class,
        Options::class,
        PageHook::class,
        Partial::class,
        PostType::class,
        Route::class,
        TabMetabox::class,
        Taxonomy::class,
        User::class,
        View::class
    ];

    /**
     * Liste des packages additionnels (extensions)
     * @return array
     */
    protected $plugins = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $app = $this->getContainer()->resolve(App::class);

        foreach ($this->getBootables() as $bootable) :
            $class = $this->getContainer()->resolve($bootable, [$app]);
        endforeach;

        $this->getContainer()->singleton(
            'tiFyLogger',
            function () {
                return Logger::globalReport();
            }
        );
        $this->getContainer()->singleton(
            'tiFyRequest',
            function () {
                return Request::capture();
            }
        );

        do_action('after_setup_tify');
    }

    /**
     * Récupération de la liste des services lancés au démarrage.
     *
     * @return array
     */
    public function getBootables()
    {
        return array_merge(
            [
                AssetsInterface::class
            ],
            $this->components,
            $this->plugins
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return tiFy
     */
    public function getContainer()
    {
        return parent::getContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        foreach($this->components as $component) :
            array_push($this->singletons, $component);
        endforeach;

        /** @todo Modifier le chargement des plugins */
        if (!defined('TIFY_CONFIG_DIR')) :
            define('TIFY_CONFIG_DIR', get_template_directory() . '/config');
        endif;

        if (file_exists(TIFY_CONFIG_DIR . '/plugins.php')) :
            $plugins = include TIFY_CONFIG_DIR . '/plugins.php';

            foreach (array_keys($plugins) as $plugin) :
                array_push($this->plugins, $plugin);
                array_push($this->singletons, $plugin);
            endforeach;
        endif;

        parent::parse();
    }
}