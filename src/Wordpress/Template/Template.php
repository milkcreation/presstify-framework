<?php

namespace tiFy\Wordpress\Template;

use tiFy\Contracts\Template\{TemplateFactory, TemplateManager};
use tiFy\Template\Templates\FileManager\Contracts\IconSet as IconSetContract;
//use WP_Screen;

class Template
{
    /**
     * Instance du gestionnaire de routage.
     * @var TemplateManager
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param TemplateManager $manager Instance du gestionnaire de routage.
     *
     * @return void
     */
    public function __construct(TemplateManager $manager)
    {
        $this->manager = $manager;

        $prefix = '/';
        if (is_multisite()) {
            $prefix = get_blog_details()->path !== '/'
                ? rtrim(preg_replace('#^' . url()->rewriteBase() . '#', '', get_blog_details()->path), '/')
                : '/';
        }

        $this->manager->setUrlPrefix($prefix)->prepareRoutes();

        foreach(config('template', []) as $name => $attrs) {
            $this->manager->register($name, $attrs);
        }

        // Surcharge de fournisseurs de service.
        /*$this->manager->getContainer()->add(IconSetContract::class, function () {
            return new Templates\FileBrowser\IconSet();
        });*/
        events()->listen('template.factory.boot', function (string $name, TemplateFactory $factory){
                add_action('admin_menu', function () use ($factory) {
                    if ($admin_menu = $factory->param('wp.admin_menu')) {
                        $factory->param([
                            'wp.admin_menu' => array_merge([
                            'menu_slug'   => $factory->name(),
                            'parent_slug' => '',
                            'page_title'  => $factory->name(),
                            'menu_title'  => $factory->name(),
                            'capability'  => 'manage_options',
                            'icon_url'    => null,
                            'position'    => null,
                            'function'    => [$factory, 'display']
                        ], is_array($admin_menu) ? $admin_menu : [])]);

                        $hookname = !$factory->param('wp.admin_menu.parent_slug', '')
                            ? add_menu_page(
                                $factory->param('wp.admin_menu.page_title'),
                                $factory->param('wp.admin_menu.menu_title'),
                                $factory->param('wp.admin_menu.capability'),
                                $factory->param('wp.admin_menu.menu_slug'),
                                $factory->param('wp.admin_menu.function'),
                                $factory->param('wp.admin_menu.icon_url'),
                                $factory->param('wp.admin_menu.position')
                            )
                            : add_submenu_page(
                                $factory->param('wp.admin_menu.parent_slug'),
                                $factory->param('wp.admin_menu.page_title'),
                                $factory->param('wp.admin_menu.menu_title'),
                                $factory->param('wp.admin_menu.capability'),
                                $factory->param('wp.admin_menu.menu_slug'),
                                $factory->param('wp.admin_menu.function')
                            );

                        /*$factory->config(['_hookname' => $hookname]);
                        $factory->config([
                                'page_url' => menu_page_url(
                                    $factory->config('admin_menu.menu_slug'), false)
                            ]
                        );

                        add_action('current_screen', function (WP_Screen $wp_screen) use ($factory) {
                            if ($wp_screen->id === $factory->config('_hookname')) {
                                $factory->config(['_wp_screen', $wp_screen]);

                                $wp_screen->add_option('per_page', [
                                    'option' => $factory->param('per_page_option_name')
                                ]);

                                $factory->load();
                            }
                        });*/
                    }
                });
        });
    }
}