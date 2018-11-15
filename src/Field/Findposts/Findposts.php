<?php

namespace tiFy\Field\Findposts;

use tiFy\Field\FieldController;

class Findposts extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     * @var string $before Contenu placé avant le champ.
     * @var string $after Contenu placé après le champ.
     * @var array $attrs Liste des propriétés de la balise HTML.
     * @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     * @var string $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     * @var array $viewer Liste des attributs de configuration de la classe des gabarits d'affichage.
     * }
     * @var array
     */
    protected $attributes = [
        'before'      => '',
        'after'       => '',
        'attrs'       => [],
        'name'        => '',
        'value'       => '',
        'ajax_action' => 'field_findposts',
        'query_args'  => [],
        'viewer'      => [],
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                add_action(
                    'wp_ajax_field_findposts',
                    [$this, 'wp_ajax']
                );

                add_action(
                    'wp_ajax_nopriv_field_findposts',
                    [$this, 'wp_ajax']
                );

                add_action(
                    'wp_ajax_field_findposts_post_permalink',
                    [$this, 'getPostPermalink']
                );

                add_action(
                    'wp_ajax_nopriv_field_findposts_post_permalink',
                    [$this, 'getPostPermalink']
                );

                wp_register_style(
                    'FieldFindposts',
                    assets()->url('field/findposts/css/styles.css'),
                    181006
                );

                wp_register_script(
                    'FieldFindposts',
                    assets()->url('field/findposts/js/scripts.js'),
                    ['media'],
                    181006,
                    true
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('FieldFindposts');
        wp_enqueue_script('FieldFindposts');
    }

    /**
     * Affichage de la fenêtre modale.
     *
     * @param string $found_action Action Ajax de récupération des éléments.
     * @param array $query_args Arguments de la requête de récupération des éléments.
     *
     * @todo pagination + gestion instance multiple
     *
     * @return string
     */
    public function modal($found_action = '', $query_args = [])
    {
        // Définition des types de post
        if (!empty($query_args['post_type'])) :
            $post_types = (array)$query_args['post_type'];
            unset($query_args['post_type']);
        else :
            $post_types = get_post_types(['public' => true], 'objects');
            unset($post_types['attachment']);
            $post_types = array_keys($post_types);
        endif;

        return $this->viewer('modal', compact('found_action', 'query_args', 'post_types'));
    }

    /**
     * Récupération d'un permalien de post selon son ID.
     *
     * @return string
     */
    public function getPostPermalink()
    {
        // Traitement des arguments de requête
        $post_id = intval(request()->post('post_id', 0));
        $relative = request()->post('relative', false);
        $default = request()->post('default', site_url('/'));

        // Traitement du permalien
        $permalink = ($_permalink = get_permalink($post_id)) ? $_permalink : $default;
        if ($relative) :
            $url_path = parse_url(site_url('/'), PHP_URL_PATH);
            $permalink = $url_path . preg_replace('/' . preg_quote(site_url('/'), '/') . '/', '', $permalink);
        endif;

        wp_die($permalink);
    }

    /**
     * Récupération de la reponse via Ajax
     *
     * @return string
     */
    public function wp_ajax()
    {
        check_ajax_referer('FieldFindposts' . request()->post('id'));

        $post_types = get_post_types(['public' => true], 'objects');
        unset($args['post_type']['attachment']);

        $s = \wp_unslash(request()->post('ps'));
        $args = [
            'post_type'      => array_keys($post_types),
            'post_status'    => 'any',
            'posts_per_page' => 50,
        ];
        $args = wp_parse_args(request()->post('query_args', []), $args);

        if ('' !== $s) :
            $args['s'] = $s;
        endif;

        $posts_query = new \WP_Query;
        $posts = $posts_query->query($args);

        if (!$posts) :
            wp_send_json_error(__('No items found.'));
        endif;

        $alt = 'alternate';
        
        /**
         * @var \WP_Post $post
         */
        foreach ($posts as &$post) :
            $post = $post->to_array();
            $post['_post_title'] = trim($post['post_title']) ? $post['post_title'] : __('(no title)');

            switch ($post['post_status']) :
                case 'publish' :
                case 'private' :
                    $post['_post_status'] = __('Published');
                    break;
                case 'future' :
                    $post['_post_status'] = __('Scheduled');
                    break;
                case 'pending' :
                    $post['_post_status'] = __('Pending Review');
                    break;
                case 'draft' :
                    $post['_post_status'] = __('Draft');
                    break;
                default:
                    $post['_post_status'] = '';
                    break;
            endswitch;

            $post['_post_date'] = ('0000-00-00 00:00:00' == $post['post_date']) ? '' : mysql2date(__('Y/m/d'), $post['post_date']);
        endforeach;

        \wp_send_json_success($this->viewer('response', compact('post_types', 'posts', 'alt'))->render());
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        static $init;

        if (!$init++) :
            add_action('admin_footer', function () {
                echo $this->modal($this->get('ajax_action'), $this->get('query_args'));
            });
        endif;

        return parent::display();
    }
}