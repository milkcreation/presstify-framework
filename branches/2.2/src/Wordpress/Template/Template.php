<?php declare(strict_types=1);

namespace tiFy\Wordpress\Template;

use Pollen\Event\TriggeredEventInterface;
use tiFy\Contracts\Template\{TemplateFactory as TemplateFactoryContract, TemplateManager};
use tiFy\Template\Templates\FileManager\Contracts\IconSet as IconSetContract;
use tiFy\Wordpress\Template\Templates\PostListTable\Contracts\{
    DbBuilder as PostListTableDbBuilderContract,
    Db as PostListTableDbContract,
    Item as PostListTableItemContract,
    Params as PostListTableParamsContract
};
use tiFy\Wordpress\Template\Templates\UserListTable\Contracts\{
    DbBuilder as UserListTableDbBuilderContract,
    Db as UserListTableDbContract,
    Item as UserListTableItemContract
};
use tiFy\Wordpress\Template\Templates\PostListTable\{
    DbBuilder as PostListTableDbBuilder,
    Db as PostListTableDb,
    Item as PostListTableItem,
    Params as PostListTableParams
};
use tiFy\Wordpress\Template\Templates\UserListTable\{
    DbBuilder as UserListTableDbBuilder,
    Db as UserListTableDb,
    Item as UserListTableItem
};
use tiFy\Wordpress\Template\Templates\{
    FileManager\IconSet,
};
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

        $this->manager->prepareRoutes();

        foreach (config('template', []) as $name => $attrs) {
            $this->manager->register($name, $attrs);
        }

        /**
         * // PostTable
         * PostListTableDbContract::class,
         * PostListTableItemContract::class,
         * PostListTableParamsContract::class,
         * PostListTableDbBuilderContract::class,
         * // UserTable
         * UserListTableDbContract::class,
         * UserListTableItemContract::class,
         * UserListTableDbBuilderContract::class,
         */

        // Surcharge de fournisseurs de service.
        $this->manager->getContainer()->add(
            IconSetContract::class,
            function () {
                return new IconSet();
            }
        );

        $this->manager->getContainer()->add(
            PostListTableDbContract::class,
            function () {
                return new PostListTableDb();
            }
        );

        $this->manager->getContainer()->add(
            PostListTableDbBuilderContract::class,
            function () {
                return new PostListTableDbBuilder();
            }
        );

        $this->manager->getContainer()->add(
            PostListTableItemContract::class,
            function () {
                return new PostListTableItem();
            }
        );

        $this->manager->getContainer()->add(
            PostListTableParamsContract::class,
            function () {
                return new PostListTableParams();
            }
        );

        $this->manager->getContainer()->add(
            UserListTableDbContract::class,
            function () {
                return new UserListTableDb();
            }
        );

        $this->manager->getContainer()->add(
            UserListTableDbBuilderContract::class,
            function () {
                return new UserListTableDbBuilder();
            }
        );

        $this->manager->getContainer()->add(
            UserListTableItemContract::class,
            function () {
                return new UserListTableItem();
            }
        );

        events()->listen(
            'template.factory.boot',
            function (TriggeredEventInterface $event, string $name, TemplateFactoryContract $factory) {
                add_action(
                    'admin_menu',
                    function () use ($factory) {
                        if ($admin_menu = $factory->param('wordpress.admin_menu')) {
                            $factory->param(
                                [
                                    'wordpress.admin_menu' => array_merge(
                                        [
                                            'menu_slug'   => $factory->name(),
                                            'parent_slug' => '',
                                            'page_title'  => $factory->label('all_items'),
                                            'menu_title'  => $factory->label()->plural(true),
                                            'capability'  => 'manage_options',
                                            'icon_url'    => null,
                                            'position'    => null,
                                            'function'    => [$factory, 'display'],
                                        ],
                                        is_array($admin_menu) ? $admin_menu : []
                                    ),
                                ]
                            );

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
                                    $factory->param('wordpress.admin_menu.function'),
                                    $factory->param('wordpress.admin_menu.position')
                                );
                            $factory->url()->setDisplayUrl(menu_page_url($factory->name(), false));

                            add_action(
                                'current_screen',
                                function (WP_Screen $wp_screen) use ($factory, $hookname) {
                                    if ($wp_screen->id === $hookname) {
                                        $factory->prepare();
                                    }
                                }
                            );
                        }
                    }
                );
            }
        );
    }
}