<?php

namespace tiFy\Wp\PageHook;

use tiFy\Contracts\Wp\PageHookItem as PageHookItemContract;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Wp\Query\QueryPost;
use WP_Post;
use WP_Screen;

class PageHookItem extends ParamsBag implements PageHookItemContract
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration.
     * @var array {
     *      Liste des attributs de configuration.
     *
     * @var string $name Nom de qualification d'enregistrement en base de donnée.
     * @var string $title Intitulé de qualification.
     * @var string $desc Texte de description.
     * @var string $object_type Type d'objet Wordpress. post|taxonomy @todo
     * @var string $object_name Nom de qualification de l'objet Wordpress. (ex. post|page|category|tag ...)
     * @var int $id Identifiant de qualification de la page d'accroche associée.
     * @var string list_order Ordre d'affichage de la liste de selection de l'interface d'administration
     * @var string show_option_none Intitulé de la liste de selection de l'interface d'administration lorsqu'aucune
     *     relation n'a été établie
     * }
     */
    protected $attributes = [
        'option_name'         => '',
        'title'               => '',
        'desc'                => '',
        'object_type'         => 'post',
        'object_name'         => 'page',
        'id'                  => 0,
        'listorder'           => 'menu_order, title',
        'show_option_none'    => '',
        'display_post_states' => true,
        'edit_form_notice'    => true,
        'rewrite'             => false,
    ];

    /**
     * Instance du post associé.
     * @var QueryPost
     */
    protected $post;

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

        parent::__construct($attrs);

        add_filter('display_post_states', function (array $post_states, WP_Post $post) {
            if (($label = $this->get('display_post_states')) && $this->is($post)) :
                if (!is_string($label)) :
                    $label = $this->getTitle();
                endif;
                $post_states[] = $label;
            endif;

            return $post_states;
        }, 10, 2);

        add_action('edit_form_top', function (WP_Post $post) {
            if (($label = $this->get('edit_form_notice')) && $this->is($post)) :
                if (!is_string($label)) :
                    $label = sprintf(__('Vous éditez actuellement : %s.', 'tify'), $this->getTitle());
                endif;
                echo "<div class=\"notice notice-info inline\">\n\t<p>{$label}</p>\n</div>";
            endif;
        });

        add_action('init', function () {
            if ($rewrite = $this->get('rewrite')) :
                if (preg_match('/(.*)@post_type/', $rewrite,  $matches) && post_type_exists($matches[1])) :
                    global $wp_rewrite, $wp_post_types;

                    $post_type = $matches[1];

                    $wp_post_types[$post_type]->rewrite = false;

                    add_rewrite_rule(
                        $this->post()->post_name . '/([^/]+)/?$',
                        'index.php?post_type='. $post_type .'&name=$matches[1]',
                        'top'
                    );

                    if ($this->post()->post_type === 'page') :
                        add_rewrite_rule(
                            $this->post()->post_name . '/' . $wp_rewrite->pagination_base . '/([0-9]{1,})/?$',
                            'index.php?page_id='. $this->post()->ID . '&paged=$matches[1]',
                            'top'
                        );
                    else :
                        add_rewrite_rule(
                            $this->post()->post_name . '/' . $wp_rewrite->pagination_base . '/([0-9]{1,})/?$',
                            'index.php?p='. $this->post()->ID . '&post_type='. $this->post()->post_type .'&paged=$matches[1]',
                            'top'
                        );
                    endif;

                    add_filter('post_type_link', function (string $post_link, WP_Post $post) use ($post_type) {
                        if ($post->post_type === $post_type) :
                            return $this->post()->getPermalink() . $post->post_name;
                        endif;

                        return $post_link;
                    }, 99999, 2);


                    add_action('current_screen',  function (WP_Screen $wp_screen) {
                        if ($wp_screen->id !== 'settings_page_tify_options') :
                            flush_rewrite_rules();
                        endif;
                    });

                    add_action('save_post', function(int $post_id) {
                        if ($this->is($post_id)) :
                            flush_rewrite_rules();
                        endif;
                    }, 999999);
                endif;
            endif;
        }, 9999999);

    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'option_name'      => 'page_hook_' . $this->name,
            'title'            => $this->name,
            'desc'             => '',
            'object_type'      => 'post',
            'object_name'      => 'page',
            'id'               => 0,
            'listorder'        => 'menu_order, title',
            'show_option_none' => __('Aucune page choisie', 'tify'),
        ];
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
        return $this->get('title');
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
    public function post()
    {
        if (is_null($this->post)) :
            if (!$post_id = $this->get('id')) :
                $this->set('id', $post_id = (int)get_option($this->get('option_name'), 0));
            endif;

            $this->post = ($post = get_post($post_id))
                ? new QueryPost($post) : false;
        endif;

        return $this->post;
    }

    /**
     * @inheritdoc
     */
    public function is($post = null)
    {
        return ($this->exists() && ($post = get_post($post)))
             ? ($this->post()->getId() === $post->ID) : false;
    }
}