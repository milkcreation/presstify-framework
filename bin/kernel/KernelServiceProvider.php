<?php

namespace tiFy\Kernel;

use App\App;

use tiFy\AdminView\AdminView;
use tiFy\AjaxAction\AjaxAction;
use tiFy\Api\Api;
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

use tiFy\Kernel\Assets\AssetsInterface;
use tiFy\Kernel\ClassInfo\ClassInfo;
use tiFy\Kernel\Composer\ClassLoader;
use tiFy\Kernel\Config\Config;
use tiFy\Kernel\Events\EventsInterface;
use tiFy\Kernel\Http\Request;
use tiFy\Kernel\Filesystem\Paths;
use tiFy\Kernel\Logger\Logger;
use tiFy\Kernel\Service;

use tiFy\Kernel\Container\ServiceProvider;

class KernelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $singletons = [
        App::class,
        AssetsInterface::class => \tiFy\Kernel\Assets\Assets::class,
        Config::class,
        ClassLoader::class,
        Column::class,
        Cron::class,
        Db::class,
        EventsInterface::class => \tiFy\Kernel\Events\Events::class,
        Field::class,
        Form::class,
        Media::class,
        Metabox::class,
        Metadata::class,
        MetaTag::class,
        Options::class,
        PageHook::class,
        Partial::class,
        Paths::class,
        PostType::class,
        Route::class,
        TabMetabox::class,
        Taxonomy::class,
        User::class,
        View::class
    ];

    /**
     * {@inheritdoc}
     */
    protected $bindings = [
        ClassInfo::class
    ];

    /**
     * Liste des packages additionnels (plugins)
     * @return array
     */
    protected $plugins = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        foreach($this->getBootables() as $bootable) :
            $class = $this->getContainer()->resolve($bootable);
        endforeach;

        //do_action('after_setup_tify');
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
                /** Ultra-prioritaire */
                Paths::class,
                Config::class,
                ClassLoader::class,
                /** ----------------- */
                App::class,
                AssetsInterface::class,
                //Column::class,
                //Cron::class,
                //Db::class,
                //Field::class,
                //Form::class,
                //Media::class,
                //Metabox::class,
                //Metadata::class,
                //MetaTag::class,
                //Options::class,
                //PageHook::class,
                //Partial::class,
                //PostType::class,
                //Route::class,
                //TabMetabox::class,
                //Taxonomy::class,
                //User::class,
                //View::class
            ],
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
    public function getSingletons()
    {
        $this->singletons += [
            'tiFyLogger' => function() {
                return Logger::globalReport();
            },
            'tiFyRequest' => function() {
                return Request::capture();
            }
        ];

        /** @todo Modifier le chargement des plugins */
        if (!defined('TIFY_CONFIG_DIR')) :
            define('TIFY_CONFIG_DIR', get_template_directory() . '/config');
        endif;

        if (file_exists(TIFY_CONFIG_DIR . '/plugins.php')) :
            $plugins = include TIFY_CONFIG_DIR . '/plugins.php';
            foreach(array_keys($plugins) as $plugin) :
                array_push($this->plugins, $plugin);
                array_push($this->singletons, $plugin);
            endforeach;
        endif;

        return $this->singletons;
    }
}