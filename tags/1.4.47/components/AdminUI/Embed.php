<?php

/**
 * Désactivation de la prise en charge d'intégration de contenu provenant de services tiers.
 *
 * @see https://codex.wordpress.org/Embeds
 * @see https://kinsta.com/knowledgebase/disable-embeds-wordpress/#inline-embed-js
 */

namespace tiFy\Components\AdminUI;

use tiFy\App\Traits\App as TraitsApp;

class Embed
{
    use TraitsApp;

    /**
     * Liste des options de désactivation des éléments de l'embed.
     * @var array
     */
    protected $options = [
        'register_route'    => true,
        'discover'          => true,
        'filter_result'     => true,
        'discovery_links'   => true,
        'host_js'           => true,
        'tiny_mce_plugin'   => true,
        'pre_oembed_result' => true,
        'rewrite_rules'     => true,
        'dequeue_script'    => true
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($options)
    {
        $this->options = ($options === true) ? $this->options : array_merge($this->options, $options);

        $this->appAddAction('init', null, 9999);
    }

    /**
     * Initialisation globale.
     *
     * @return void
     */
    final public function init()
    {
        // Remove the REST API endpoint.
        if ($this->options['register_route']) :
            remove_action('rest_api_init', 'wp_oembed_register_route');
        endif;

        // Turn off oEmbed auto discovery.
        if ($this->options['discover']) :
            add_filter('embed_oembed_discover', '__return_false');
        endif;

        // Don't filter oEmbed results.
        if ($this->options['filter_result']) :
            remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        endif;

        // Remove oEmbed discovery links.
        if ($this->options['discovery_links']) :
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
        endif;

        // Remove oEmbed-specific JavaScript from the front-end and back-end.
        if ($this->options['host_js']) :
            remove_action('wp_head', 'wp_oembed_add_host_js');
        endif;
        if ($this->options['tiny_mce_plugin']) :
            $this->appAddFilter('tiny_mce_plugins');
        endif;

        // Remove filter of the oEmbed result before any HTTP requests are made.
        if ($this->options['pre_oembed_result']) :
            remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
        endif;

        // Retire les régles de réécriture.
        if ($this->options['rewrite_rules']) :
            $this->appAddFilter('rewrite_rules_array');
        endif;

        // Retire le script d'intégration de la file.
        if ($this->options['dequeue_script']) :
            $this->appAddAction('wp_footer');
        endif;
    }

    /**
     * Filtrage de la liste des plugins tinyMCE.
     *
     * @param array $plugins Liste des plugins tinyMCE actifs.
     *
     * @return array
     */
    final public function tiny_mce_plugins($plugins)
    {
        return array_diff($plugins, ['wpembed']);
    }

    /**
     * Filtage de la liste des règles de réécriture.
     *
     * @param array $rules Liste des règles de réécriture.
     *
     * @return array
     */
    final public function rewrite_rules_array($rules)
    {
        foreach ($rules as $rule => $rewrite) :
            if (false !== strpos($rewrite, 'embed=true')) :
                unset($rules[$rule]);
            endif;
        endforeach;

        return $rules;
    }

    /**
     * Gestion des éléments du pied de page.
     *
     * @return void
     */
    function wp_footer()
    {
        wp_dequeue_script('wp-embed');
    }
}