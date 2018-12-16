<?php

namespace tiFy\View;

use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\Contracts\Container\ServiceProviderInterface;
use tiFy\Contracts\View\ViewPatternController as ViewPatternControllerContract;
use tiFy\View\Pattern\PatternServiceProvider;
use tiFy\tiFy;

class ViewPatternController implements ViewPatternControllerContract
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du controleur de traitement des attributs de configuration.
     * @var ParamsBag
     */
    protected $config;

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        PatternServiceProvider::class
    ];

    /**
     * CONSTRUCTEUR
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return void
     */
    public function __construct($attrs = [])
    {
        $this->config = params($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($name)
    {
        return $this->_boot($name);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * {@inheritdoc}
     */
    private function _boot($name)
    {
        $this->name = $name;

        foreach ($this->getServiceProviders() as $serviceProvider) :
            $resolved = new $serviceProvider($this->getContainer(), $this);

            if ($resolved instanceof ServiceProviderInterface) :
                $this->getContainer()->addServiceProvider($resolved);
            endif;
        endforeach;

        add_action(
            'admin_menu',
            function () {
                if ($attrs = $this->config('admin_menu', [])) :
                    $this->config(
                        [
                            'admin_menu' => array_merge(
                                [
                                    'menu_slug'   => $this->name(),
                                    'parent_slug' => '',
                                    'page_title'  => $this->name(),
                                    'menu_title'  => $this->name(),
                                    'capability'  => 'manage_options',
                                    'icon_url'    => null,
                                    'position'    => null,
                                    'function'    => [$this, 'display']
                                ],
                                $attrs
                            )
                        ]
                    );

                    $hookname = !$this->config('admin_menu.parent_slug')
                        ? add_menu_page(
                            $this->config('admin_menu.page_title'),
                            $this->config('admin_menu.menu_title'),
                            $this->config('admin_menu.capability'),
                            $this->config('admin_menu.menu_slug'),
                            $this->config('admin_menu.function'),
                            $this->config('admin_menu.icon_url'),
                            $this->config('admin_menu.position')
                        )
                        : add_submenu_page(
                            $this->config('admin_menu.parent_slug'),
                            $this->config('admin_menu.page_title'),
                            $this->config('admin_menu.menu_title'),
                            $this->config('admin_menu.capability'),
                            $this->config('admin_menu.menu_slug'),
                            $this->config('admin_menu.function')
                        );

                    $this->config(['_hookname' => $hookname]);
                    $this->config(['page_url' => menu_page_url($this->config('admin_menu.menu_slug'), false)]);

                    add_action(
                        'current_screen',
                        function (\WP_Screen $wp_screen) {
                            if ($wp_screen->id === $this->config('_hookname')) :
                                $this->config(['_wp_screen',  $wp_screen]);

                                $wp_screen->add_option(
                                    'per_page',
                                    [
                                        'option' => $this->param('per_page_option_name')
                                    ]
                                );

                                $this->load();
                            endif;
                        }
                    );

                endif;
            }
        );

        $this->boot();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function assets()
    {
        return $this->resolve('assets');
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        //var_dump($this->url()->without(['paged'])); exit;
    }

    /**
     * {@inheritdoc}
     */
    public function config($key = null, $default = null)
    {
        if (is_null($key)) :
            return $this->config;
        elseif(is_array($key)) :
            foreach($key as $k => $value) :
                $this->config->set($k, $value);
            endforeach;

            return $this;
        else :
            return $this->config->get($key, $default);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function db()
    {
        return $this->resolve('db');
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        echo (string)$this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function extend($alias)
    {
        return $this->getContainer()->extend("view.pattern.{$this->name()}.{$alias}");
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return tiFy::instance();
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function bound($abstract)
    {
        return $this->getContainer()->has("view.pattern.{$this->name()}.{$abstract}");
    }

    /**
     * {@inheritdoc}
     */
    public function label($key = null, $default = '')
    {
        $labels = $this->resolve('labels');

        if (is_null($key)) :
            return $labels;
        endif;

        return $labels->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $this->process();
        $this->prepare();
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function notices()
    {
        return $this->resolve('notices');
    }

    /**
     * {@inheritdoc}
     */
    public function param($key = null, $default = null)
    {
        $params = $this->resolve('params');

        if (is_null($key)) :
            return $params;
        elseif (is_array($key)) :
            foreach($key as $k => $value) :
                $params->set($k, $value);
            endforeach;

            return $this;
        else :
            return $params->get($key, $default);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return __('Aucun contenu à afficher', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function request()
    {
        return $this->resolve('request');
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($abstract, array $args = [])
    {
        return $this->getContainer()->get("view.pattern.{$this->name()}.{$abstract}", $args);
    }

    /**
     * {@inheritdoc}
     */
    public function url()
    {
        return $this->resolve('url');
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        $viewer = $this->resolve('viewer');

        if (func_num_args() === 0) :
            return $viewer;
        endif;

        return $viewer->make("_override::{$view}", $data);
    }
}