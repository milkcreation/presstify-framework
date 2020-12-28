<?php declare(strict_types=1);

namespace tiFy\Wordpress\Metabox;

use Exception;
use tiFy\Contracts\Metabox\MetaboxDriver;
use tiFy\Contracts\Metabox\FilefeedDriver as FilefeedDriverContract;
use tiFy\Contracts\Metabox\ImagefeedDriver as ImagefeedDriverContract;
use tiFy\Contracts\Metabox\Metabox as MetaboxContract;
use tiFy\Contracts\Metabox\VideofeedDriver as VideofeedDriverContract;
use tiFy\Wordpress\Metabox\Context\SideContext;
use tiFy\Wordpress\Metabox\Driver\Filefeed\Filefeed;
use tiFy\Wordpress\Metabox\Driver\Imagefeed\Imagefeed;
use tiFy\Wordpress\Metabox\Driver\Videofeed\Videofeed;
use tiFy\Wordpress\Routing\WpScreen;
use tiFy\Support\Proxy\PostType;
use tiFy\Support\Proxy\Request;
use tiFy\Support\Proxy\Taxonomy;
use tiFy\Support\Proxy\User;
use WP_Post, WP_Screen, WP_Term, WP_User;

class Metabox
{
    /**
     * Instance du gestionnaire utilisateur.
     * @var MetaboxContract
     */
    private $manager;

    /**
     * Liste des indices de qualification de données de post.
     * @var array
     */
    protected $postKeys = [
        'ID',
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content',
        'post_title',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_password',
        'post_name',
        'to_ping',
        'pinged',
        'post_modified',
        'post_modified_gmt',
        'post_content_filtered',
        'post_parent',
        'guid',
        'menu_order',
        'post_type',
        'post_mime_type',
        'comment_count',
    ];

    /**
     * Liste des indices de qualification de données de terme de taxonomie.
     * @var array
     */
    protected $termKeys = [
        'description',
        'name',
        'parent',
        'term_id',
        'slug',
    ];

    /**
     * Liste des indices de qualification de données utilisateur.
     * @var array
     */
    protected $userKeys = [
        'ID',
        'login',
        'first_name',
        'last_name',
    ];

    /**
     * @param MetaboxContract $manager Instance du gestionnaire de boîtes de saisie.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(MetaboxContract $manager)
    {
        $this->manager = $manager;

        $this->registerOverride();

        $this->manager->boot();

        add_action('current_screen', function (WP_Screen $wp_screen) {
            $screen = new WpScreen($wp_screen);

            if ($screen->getObjectType() === 'post_type') {
                $type = $screen->getObjectName();
                $drivers = [];

                if ($metaboxScreen = $this->manager->getScreen("{$type}@post_type")) {
                    $drivers = $metaboxScreen->getDrivers();
                }

                array_walk($drivers, function (MetaboxDriver $driver) use ($type) {
                    if ($driver->getContext()->getAlias() === 'side') {
                        add_action('add_meta_boxes', function () use ($driver) {
                            add_meta_box(
                                $driver->getAlias(),
                                $driver->title(), function (...$args) use ($driver) {
                                echo $driver->handle($args)->render();
                            },
                                null,
                                'side'
                            );
                        });
                    }

                    $key = $driver->name();

                    if ($key && !in_array($key, $this->postKeys) && !PostType::meta()->exists($type, $key)) {
                        PostType::meta()->registerSingle($type, $key);
                    }
                });
            } elseif (($screen->getHookname() === 'options')) {
                $option_page = Request::input('option_page', '');
                $drivers = [];

                if ($metaboxScreen = $this->manager->getScreen("{$option_page}@options")) {
                    $drivers = $metaboxScreen->getDrivers();
                    add_filter('allowed_options', function ($allowed_options) use ($option_page) {
                        if (!isset($allowed_options[$option_page])) {
                            $allowed_options[$option_page] = [];
                        }

                        return $allowed_options;
                    });
                }

                array_walk($drivers, function (MetaboxDriver $driver) use ($option_page) {
                    if ($name = $driver->name()) {
                        register_setting($option_page, $name);
                    }
                });
            } elseif ($screen->getObjectType() === 'taxonomy') {
                $tax = $screen->getObjectName();
                $drivers = [];

                if ($metaboxScreen = $this->manager->getScreen("{$tax}@taxonomy")) {
                    $drivers = $metaboxScreen->getDrivers();
                }

                array_walk($drivers, function (MetaboxDriver $driver) use ($tax) {
                    $key = $driver->name();

                    if ($key && !in_array($key, $this->termKeys) && !Taxonomy::meta()->exists($tax, $key)) {
                        Taxonomy::meta()->registerSingle($tax, $key);
                    }
                });
            } elseif ($screen->getObjectType() === 'user') {
                //$roles = $screen->getObjectName();
                $drivers = [];

                if ($metaboxScreen = $this->manager->getScreen("@user")) {
                    $drivers = $metaboxScreen->getDrivers();
                }

                array_walk($drivers, function (MetaboxDriver $driver) {
                    $key = $driver->name();

                    if ($key && !in_array($key, $this->userKeys) && !User::meta()->exists($key)) {
                        User::meta()->registerSingle($key);
                    }
                });
            }

            if ($screen->getObjectType()) {
                $drivers = $this->manager->all();
                $tabRdr = function (...$args) {
                    echo $this->manager->render('tab', $args);
                };

                switch ($screen->getObjectType()) {
                    case 'post_type' :
                        /** 'edit_form_top',
                         * 'edit_form_before_permalink',
                         * 'edit_form_after_title',
                         * 'edit_form_after_editor',
                         * 'submitpage_box',
                         * 'submitpost_box',
                         * 'edit_page_form',
                         * 'edit_form_advanced',
                         * 'dbx_post_sidebar' */
                        if ($screen->getScreen()->is_block_editor()) {
                            add_meta_box('blockEditor-metabox', __('Réglages', 'tify'), $tabRdr);
                        } else {
                            add_action(
                                $screen->getObjectName() === 'page' ? 'edit_page_form' : 'edit_form_advanced',
                                $tabRdr
                            );
                        }

                        array_walk($drivers, function (MetaboxDriver $driver) {
                            $driver->setHandler(function (MetaboxDriver $driver, WP_Post $wp_post) {
                                $driver->set('wp_post', $wp_post);

                                if (is_null($driver->value()) && ($name = $driver->name())) {
                                    if (in_array($name, $this->postKeys)) {
                                        $driver['value'] = $wp_post->{$name};
                                    } else {
                                        $driver['value'] = get_post_meta($wp_post->ID, $driver->name(), true);
                                    }
                                }
                            });
                        });
                        break;
                    case 'options' :
                        add_settings_section('navtab', null, $tabRdr, $screen->getObjectName());

                        array_walk($drivers, function (MetaboxDriver $driver) {
                            $driver->setHandler(function (MetaboxDriver $driver) {
                                if (is_null($driver->value()) && ($name = $driver->name())) {
                                    $driver['value'] = get_option($name);
                                }
                            });
                        });
                        break;
                    case 'taxonomy' :
                        add_action($screen->getObjectName() . '_edit_form', $tabRdr, 10, 2);

                        array_walk($drivers, function (MetaboxDriver $driver) {
                            $driver->setHandler(function (MetaboxDriver $driver, WP_Term $wp_term) {
                                $driver->set('wp_term', $wp_term);

                                if ($name = $driver->name()) {
                                    if (in_array($name, $this->termKeys)) {
                                        $driver['value'] = $wp_term->{$name};
                                    } else {
                                        $driver['value'] = get_term_meta($wp_term->term_id, $driver->name(), true);
                                    }
                                }
                            });
                        });
                        break;
                    case 'user' :
                        add_action('show_user_profile', $tabRdr);
                        add_action('edit_user_profile', $tabRdr);

                        array_walk($drivers, function (MetaboxDriver $driver) {
                            $driver->setHandler(function (MetaboxDriver $driver, WP_User $wp_user) {
                                $driver->set('wp_user', $wp_user);

                                if ($name = $driver->name()) {
                                    if (in_array($name, $this->userKeys)) {
                                        $driver['value'] = $wp_user->{$name};
                                    } else {
                                        $driver['value'] = get_user_meta($wp_user->ID, $driver->name(), true);
                                    }
                                }
                            });
                        });
                        break;
                }
            }
        });

        /*
        add_action('add_meta_boxes', function () {
            foreach ($this->removes as $screen => $items) {
                if (preg_match('/(.*)@(post_type|taxonomy|user)/', $screen)) {
                    $screen = 'edit::' . $screen;
                }
                $WpScreen = WpScreen::get($screen);

                foreach ($items as $id => $contexts) {
                    foreach ($contexts as $context) {
                        remove_meta_box($id, $WpScreen->getObjectName(), $context);
                    }

                    // Hack Wordpress : Maintient du support de la modification du permalien.
                    if ($id === 'slugdiv' && ($WpScreen->getObjectType() === 'post_type')) {
                        $post_type = $WpScreen->getObjectName();

                        add_action('edit_form_before_permalink', function ($post) use ($post_type) {
                            if ($post->post_type !== $post_type) {
                                return;
                            }

                            $editable_slug = apply_filters('editable_slug', $post->post_name, $post);

                            echo field('hidden', [
                                'name'  => 'post_name',
                                'value' => esc_attr($editable_slug),
                                'attrs' => [
                                    'id'           => 'post_name',
                                    'autocomplete' => 'off',
                                ],
                            ]);
                        });
                    }
                }
            }
        }, 999999); */
    }

    /**
     * Déclaration des controleurs de surchage.
     *
     * @return void
     */
    public function registerOverride(): void
    {
        app()->add('metabox.screen', function () {
            return new MetaboxScreen();
        });

        app()->add('metabox.context.side', function () {
            return new SideContext();
        });

        app()->share(FilefeedDriverContract::class, function () {
            return new Filefeed();
        });

        app()->share(ImagefeedDriverContract::class, function () {
            return new Imagefeed();
        });

        app()->share(VideofeedDriverContract::class, function () {
            return new Videofeed();
        });
    }
}