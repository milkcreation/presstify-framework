<?php declare(strict_types=1);

namespace tiFy\Wordpress\Metabox;

use tiFy\Contracts\Metabox\MetaboxManager;
use tiFy\Wordpress\Routing\WpScreen;
use tiFy\Wordpress\Contracts\WpScreen as WpScreenContract;
use WP_Screen;

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

        /*
        add_action('wp_loaded', function () {
            foreach (config('metabox', []) as $screen => $items) {
                if (!is_null($screen) && preg_match('#(.*)@(post_type|taxonomy|user)#', $screen)) {
                    $screen = 'edit::' . $screen;
                }

                foreach ($items as $name => $attrs) {
                    $this->items[] = app()->get('metabox.factory', [$name, $attrs, $screen]);
                }
            }
        }, 0);

        add_action('current_screen', function ($wp_current_screen) {
            $this->screen = wordpress()->wp_screen($wp_current_screen);

            $attrs = [];
            foreach ($this->tabs as $screen => $_attrs) {
                if (preg_match('#(.*)@(post_type|taxonomy|user)#', $screen)) {
                    $screen = 'edit::' . $screen;
                }
                $WpScreen = WpScreen::get($screen);

                if ($WpScreen->getHookname() === $this->screen->getHookname()) {
                    $attrs = $_attrs;
                    break;
                }
            }

            // @var WP_Screen $wp_current_screen
            foreach ($this->items as $item) {
                $item->load($this->screen);
            }

            app()->get('metabox.tab', [$attrs, $this->screen]);
        }, 999999);

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
}