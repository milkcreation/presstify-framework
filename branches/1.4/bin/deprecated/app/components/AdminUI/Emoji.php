<?php

namespace tiFy\Components\AdminUI;

use tiFy\App\Traits\App as TraitsApp;

class Emoji
{
    use TraitsApp;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->appAddAction('init');
        $this->appAddFilter('tiny_mce_plugins');
        $this->appAddFilter('wp_resource_hints', null, 10, 2);
    }

    /**
     * Initialisation globale.
     *
     * @return void
     */
    final public function init()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }

    /**
     * Filtrage de la liste des plugins tinyMCE.
     *
     * @param array $plugins Liste des plugins tinyMCE actifs.
     *
     * @return array
     */
    function tiny_mce_plugins($plugins)
    {
        if (is_array($plugins)) :
            return array_diff($plugins, ['wpemoji']);
        else :
            return [];
        endif;
    }

    /**
     * Récupération de ressources pré-affichées dans le navigateur.
     *
     * @param array $urls Liste des urls des ressources pré-affichées.
     * @param string $relation_type Type de relation des urls à pré-afficher.
     *
     * @return array
     */
    function wp_resource_hints($urls, $relation_type)
    {
        if ('dns-prefetch' == $relation_type) :
            /** This filter is documented in wp-includes/formatting.php */
            $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

            $urls = array_diff($urls, [$emoji_svg_url]);
        endif;

        return $urls;
    }
}