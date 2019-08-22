<?php declare(strict_types=1);

namespace tiFy\Wordpress\Template;

use tiFy\Contracts\Template\{TemplateFactory, TemplateManager};
use tiFy\Template\Templates\FileManager\Contracts\IconSet as IconSetContract;
use tiFy\Wordpress\Template\Templates\FileManager\IconSet;
use WP_Screen;

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

        foreach (config('template', []) as $name => $attrs) {
            $this->manager->register($name, $attrs);
        }

        // Surcharge de fournisseurs de service.
        $this->manager->getContainer()->add(IconSetContract::class, function () {
            return new IconSet();
        });

        events()->listen('template.factory.boot', function (string $name, TemplateFactory $factory) {
            add_action('admin_menu', function () use ($factory) {
                if ($admin_menu = $factory->param('wordpress.admin_menu')) {
                    $factory->param([
                        'wordpress.admin_menu' => array_merge([
                            'menu_slug'   => $factory->name(),
                            'parent_slug' => '',
                            'page_title'  => $factory->label('all_items'),
                            'menu_title'  => ucfirst($factory->label()->getPlural()),
                            'capability'  => 'manage_options',
                            'icon_url'    => null,
                            'position'    => null,
                            'function'    => [$factory, 'display'],
                        ], is_array($admin_menu) ? $admin_menu : []),
                    ]);

                    $hookname = !$factory->param('wordpress.admin_menu.parent_slug', '')
                        ? add_menu_page(
                            $factory->param('wordpress.admin_menu.page_title'),
                            $factory->param('wordpress.admin_menu.menu_title'),
                            $factory->param('wordpress.admin_menu.capability'),
                            $factory->param('wordpress.admin_menu.menu_slug'),
                            $factory->param('wordpress.admin_menu.function'),
                            $factory->param('wordpress.admin_menu.icon_url'),
                            $factory->param('wordpress.admin_menu.position')
                        )
                        : add_submenu_page(
                            $factory->param('wordpress.admin_menu.parent_slug'),
                            $factory->param('wordpress.admin_menu.page_title'),
                            $factory->param('wordpress.admin_menu.menu_title'),
                            $factory->param('wordpress.admin_menu.capability'),
                            $factory->param('wordpress.admin_menu.menu_slug'),
                            $factory->param('wordpress.admin_menu.function')
                        );

                    add_action('current_screen', function (WP_Screen $wp_screen) use ($factory, $hookname) {
                        if ($wp_screen->id === $hookname) {
                            $factory->prepare();
                        }
                    });
                }
            });
        });
    }
}