<?php

namespace tiFy\Wordpress\Template;

use tiFy\Contracts\Template\TemplateManager;
use tiFy\Template\Templates\FileBrowser\Contracts\IconSet as IconSetContract;
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

        foreach(config('template', []) as $name => $attrs) {
            $this->manager->register($name, $attrs);
        }

        // Surcharge de fournisseurs de service.
        /*$this->manager->getContainer()->add(IconSetContract::class, function () {
            return new Templates\FileBrowser\IconSet();
        });*/

        events()->listen('template.factory.boot', function (/*string $name, TemplateFactory $factory*/){
            /*
            add_action('admin_menu', function () use ($factory) {
                if ($attrs = $factory->config('admin_menu', [])) {
                    $factory->config([
                        'admin_menu' => array_merge([
                            'menu_slug'   => $factory->name(),
                            'parent_slug' => '',
                            'page_title'  => $factory->name(),
                            'menu_title'  => $factory->name(),
                            'capability'  => 'manage_options',
                            'icon_url'    => null,
                            'position'    => null,
                            'function'    => [$factory, 'display']
                        ], $attrs)
                    ]);

                    $hookname = !$factory->config('admin_menu.parent_slug')
                        ? add_menu_page(
                            $factory->config('admin_menu.page_title'),
                            $factory->config('admin_menu.menu_title'),
                            $factory->config('admin_menu.capability'),
                            $factory->config('admin_menu.menu_slug'),
                            $factory->config('admin_menu.function'),
                            $factory->config('admin_menu.icon_url'),
                            $factory->config('admin_menu.position')
                        )
                        : add_submenu_page(
                            $factory->config('admin_menu.parent_slug'),
                            $factory->config('admin_menu.page_title'),
                            $factory->config('admin_menu.menu_title'),
                            $factory->config('admin_menu.capability'),
                            $factory->config('admin_menu.menu_slug'),
                            $factory->config('admin_menu.function')
                        );

                    $factory->config(['_hookname' => $hookname]);
                    $factory->config(['page_url' => menu_page_url(
                        $factory->config('admin_menu.menu_slug'), false)]
                    );

                    add_action('current_screen', function (WP_Screen $wp_screen) use ($factory) {
                        if ($wp_screen->id === $factory->config('_hookname')) {
                            $factory->config(['_wp_screen', $wp_screen]);

                            $wp_screen->add_option('per_page', [
                                'option' => $factory->param('per_page_option_name')
                            ]);

                            $factory->load();
                        }
                    });
                }
            });
            */
        });
    }
}