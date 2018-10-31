<?php

namespace tiFy\Field\Findposts;

use tiFy\Field\FieldController;

class Findposts extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var array $viewer Liste des attributs de configuration de la classe des gabarits d'affichage.
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
                    'wp_ajax'
                );

                add_action(
                    'wp_ajax_nopriv_field_findposts',
                    'wp_ajax'
                );

                wp_register_style(
                    'FieldFindposts',
                    assets()->url('field/findposts/css/styles.css'),
                    181006
                );

                wp_register_script(
                    'FieldFindposts',
                    assets()->url('field/findposts/js/scripts.css'),
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
     * Affichage de la fenêtre modale
     * @todo pagination + gestion instance multiple
     */
    public static function modal($found_action = '', $query_args = [])
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


        $s = \wp_unslash($_POST['ps']);
        $args = [
            'post_type'      => array_keys($post_types),
            'post_status'    => 'any',
            'posts_per_page' => 50,
        ];
        $args = wp_parse_args($_POST['query_args'], $args);

        if ('' !== $s) :
            $args['s'] = $s;
        endif;

        $posts_query = new \WP_Query;
        $posts = $posts_query->query($args);

        if (!$posts) :
            wp_send_json_error(__('No items found.'));
        endif;

        $html = '<table class="widefat"><thead><tr><th class="found-radio"><br /></th><th>' . __('Title') . '</th><th class="no-break">' . __('Type') . '</th><th class="no-break">' . __('Date') . '</th><th class="no-break">' . __('Status') . '</th></tr></thead><tbody>';
        $alt = '';
        foreach ($posts as $post) :
            $title = trim($post->post_title) ? $post->post_title : __('(no title)');
            $alt = ('alternate' == $alt) ? '' : 'alternate';

            switch ($post->post_status) :
                case 'publish' :
                case 'private' :
                    $stat = __('Published');
                    break;
                case 'future' :
                    $stat = __('Scheduled');
                    break;
                case 'pending' :
                    $stat = __('Pending Review');
                    break;
                case 'draft' :
                    $stat = __('Draft');
                    break;
            endswitch;

            if ('0000-00-00 00:00:00' == $post->post_date) :
                $time = '';
            else :
                /* translators: date format in table columns, see https://secure.php.net/date */
                $time = mysql2date(__('Y/m/d'), $post->post_date);
            endif;

            $html .= '<tr class="' . trim('found-posts ' . $alt) . '"><td class="found-radio"><input type="radio" id="found-' . $post->ID . '" name="found_post_id" value="' . esc_attr($post->ID) . '"></td>';
            $html .= '<td><label for="found-' . $post->ID . '">' . \esc_html($title) . '</label></td><td class="no-break">' . \esc_html($post_types[$post->post_type]->labels->singular_name) . '</td><td class="no-break">' . esc_html($time) . '</td><td class="no-break">' . \esc_html($stat) . ' </td></tr>' . "\n\n";
        endforeach;

        $html .= '</tbody></table>';

        \wp_send_json_success($html);
    }
}