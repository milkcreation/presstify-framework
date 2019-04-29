<?php

namespace tiFy\Wordpress\PageHook;

use Closure;
use tiFy\Contracts\Routing\Route;
use tiFy\Support\ParamsBag;
use tiFy\Wordpress\Contracts\PageHookItem as PageHookItemContract;
use tiFy\Wordpress\Query\QueryPost;
use WP_Post;

class PageHookItem extends ParamsBag implements PageHookItemContract
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du post associé.
     * @var QueryPost
     */
    protected $post;

    /**
     * Instance de la route associée.
     * @var Route
     */
    protected $route;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        $this->set($attrs)->parse();

        add_filter('display_post_states', function (array $post_states, WP_Post $post) {
            if (($label = $this->get('display_post_states')) && $this->is($post)) {
                if (!is_string($label)) {
                    $label = $this->getTitle();
                }
                $post_states[] = $label;
            }
            return $post_states;
        }, 10, 2);

        add_action('edit_form_top', function (WP_Post $post) {
            if (($label = $this->get('edit_form_notice')) && $this->is($post)) {
                if (!is_string($label)) {
                    $label = sprintf(__('Vous éditez actuellement : %s.', 'tify'), $this->getTitle());
                }
                echo "<div class=\"notice notice-info inline\">\n\t<p>{$label}</p>\n</div>";
            }
        });

        add_action('init', function () {
            if (($rewrite = $this->get('rewrite')) && $this->exists()) {
                if (preg_match('/(.*)@post_type/', $rewrite, $matches) && post_type_exists($matches[1])) {
                    global $wp_rewrite, $wp_post_types;

                    $post_type = $matches[1];

                    $wp_post_types[$post_type]->has_archive = true;
                    $wp_post_types[$post_type]->rewrite = false;

                    add_rewrite_rule(
                        $this->post()->post_name . '/([^/]+)/?$',
                        'index.php?post_type=' . $post_type . '&name=$matches[1]',
                        'top'
                    );

                    if ($this->post()->post_type === 'page') {
                        add_rewrite_rule(
                            $this->post()->post_name . '/' . $wp_rewrite->pagination_base . '/([0-9]{1,})/?$',
                            'index.php?page_id=' . $this->post()->ID . '&paged=$matches[1]',
                            'top'
                        );
                    } else {
                        add_rewrite_rule(
                            $this->post()->post_name . '/' . $wp_rewrite->pagination_base . '/([0-9]{1,})/?$',
                            'index.php?p=' . $this->post()->ID . '&post_type=' . $this->post()->post_type .
                            '&paged=$matches[1]',
                            'top'
                        );
                    }

                    add_filter('post_type_link', function (string $post_link, WP_Post $post) use ($post_type) {
                        if ($post->post_type === $post_type) {
                            return $this->post()->getPermalink() . $post->post_name;
                        }
                        return $post_link;
                    }, 99999, 2);

                    add_action('save_post', function (int $post_id) {
                        if ($this->is($post_id)) {
                            flush_rewrite_rules();
                        }
                    }, 999999);
                }
            }
        }, 999999);

        add_action('after_setup_theme', function () {
            if (($route = $this->get('route')) && is_callable($route) && $this->exists()) {
                $this->route = router()->get($this->getPath() . '[/page/{page:\d+}]', $route);
            }
        }, 25);
    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'id'                  => 0,
            'desc'                => '',
            'display_post_states' => true,
            'edit_form_notice'    => true,
            'listorder'           => 'menu_order, title',
            'object_type'         => 'post',
            'object_name'         => 'page',
            'option_name'         => "page_hook_{$this->name}",
            'rewrite'             => '',
            'route'               => false,
            'show_option_none'    => __('Aucune page choisie', 'tify'),
            'title'               => $this->name
        ];
    }

    /**
     * @inheritdoc
     */
    public function exists()
    {
        return $this->post() instanceof QueryPost;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->desc instanceof Closure ? call_user_func($this->desc) : $this->desc;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->exists() ? $this->post()->getPath() : '';
    }

    /**
     * @inheritdoc
     */
    public function getObjectType()
    {
        return $this->get('object_type');
    }

    /**
     * @inheritdoc
     */
    public function getObjectName()
    {
        return $this->get('object_name');
    }

    /**
     * @inheritdoc
     */
    public function getOptionName()
    {
        return $this->get('option_name');
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->title instanceof Closure ? call_user_func($this->title) : $this->title;
    }

    /**
     * @inheritdoc
     */
    public function is($post = null)
    {
        if (!$post && ($route = $this->route())) {
            return router()->current() === $route;
        } else {
            return ($this->exists() && ($post = get_post($post)))
                ? ($this->post()->getId() == $post->ID) : false;
        }
    }

    /**
     * @inheritdoc
     */
    public function post()
    {
        if (is_null($this->post)) {
            if (!$post_id = $this->get('id')) {
                $this->set('id', $post_id = (int)get_option($this->get('option_name'), 0));
            }
            $this->post = ($post_id && ($post = get_post($post_id)))
                ? new QueryPost($post) : false;
        }
        return $this->post;
    }

    /**
     * @inheritdoc
     */
    public function route()
    {
        return $this->route instanceof Route ? $this->route : null;
    }
}