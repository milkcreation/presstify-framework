<?php declare(strict_types=1);

namespace tiFy\Wordpress\Metabox;

use tiFy\Contracts\Metabox\{MetaboxDriver, MetaboxManager, MetaboxScreen as MetaboxScreenContract};
use tiFy\Wordpress\Routing\WpScreen;
use WP_Post;
use WP_Screen;
use WP_Term;
use WP_User;

class Metabox
{
    /**
     * Instance du gestionnaire utilisateur.
     * @var MetaboxManager
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param MetaboxManager $manager Instance du gestionnaire de metaboxes.
     *
     * @return void
     */
    public function __construct(MetaboxManager $manager)
    {
        $this->manager = $manager;

        $this->registerOverride();

        add_action('current_screen', function (WP_Screen $wp_screen) {
            $metaboxes = $this->manager->all();
            $screen = new WpScreen($wp_screen);
            $tabRdr = function (...$args) {
                echo $this->manager->render('tab', $args);
            };

            switch ($screen->getObjectType()) {
                case 'post_type' :
                    add_action($screen->getObjectName() === 'page' ? 'edit_page_form' : 'edit_form_advanced', $tabRdr);

                    array_walk($metaboxes, function (MetaboxDriver $box) {
                        $box->setHandler(function (MetaboxDriver $box, WP_Post $post) {
                            if (is_null($box['value'])) {
                                $box['value'] = get_post_meta($post->ID, $box->name(), true);
                            }
                        });
                    });
                    break;
                case 'options' :
                    add_settings_section('navtab', null, $tabRdr, $screen->getObjectName());

                    array_walk($metaboxes, function (MetaboxDriver $box) {
                        $box->setHandler(function (MetaboxDriver $box) {
                            if (is_null($box['value'])) {
                                $box['value'] = get_option($box->name());
                            }
                        });
                    });
                    break;
                case 'taxonomy' :
                    add_action($screen->getObjectName() . '_edit_form', $tabRdr, 10, 2);

                    array_walk($metaboxes, function (MetaboxDriver $box) {
                        $box->setHandler(function (MetaboxDriver $box, WP_Term $term, $taxonomy) {
                            if (is_null($box['value'])) {
                                $box['value'] = get_term_meta($term->term_id, $box->name(), true);
                            }
                        });
                    });
                    break;
                case 'user' :
                    add_action('show_user_profile', $tabRdr);
                    add_action('edit_user_profile', $tabRdr);

                    array_walk($metaboxes, function (MetaboxDriver $box) {
                        $box->setHandler(function (MetaboxDriver $box, WP_User $user) {
                            if (is_null($box['value'])) {
                                $box['value'] = get_user_meta($user->ID, $box->name(), true);
                            }
                        });
                    });
                    break;
            }
        });

        /*
        add_action('add_meta_boxes', function () {
            foreach ($this->removes as $screen => $items) {
                if (preg_match('#(.*)@(post_type|taxonomy|user)#', $screen)) {
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
        app()->add(MetaboxScreenContract::class, function () {
            return (new MetaboxScreen())->setManager($this->manager);
        });
    }
}