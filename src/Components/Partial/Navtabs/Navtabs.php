<?php

namespace tiFy\Components\Partial\Navtabs;

use tiFy\Kernel\Tools;
use tiFy\Partial\AbstractPartialItem;
use tiFy\Components\Partial\Navtabs\Walker;

class Navtabs extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var array $attrs Liste des attributs de balise HTML du conteneur.
     *      @var array $nodes {
     *          Liste des onglets de navigation.
     *
     *              @var string $name Nom de qualification.
     *              @var string $parent Nom de qualification de l'élément parent.
     *              @var string $attrs Liste des attributs de balise HTML du conteneur
     *              @var string|callable $content
     *              @var int $position Ordre d'affichage dans le
     *      }
     * }
     */
    protected $attributes = [
        'attrs' => [],
        'nodes' => [],
        'options' => [
            'prefix' => 'tiFyPartial-Navtabs'
        ]
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        $this->appAddAction(
            'wp_ajax_tify_partial_navtabs',
            'wp_ajax'
        );
        $this->appAddAction(
            'wp_ajax_nopriv_tify_partial_navtabs',
            'wp_ajax'
        );

        // Déclaration des scripts
        \wp_register_style(
            'tiFyPartial-Navtabs',
            $this->appAssetUrl('/Partial/Navtabs/css/styles.css'),
            [],
            170704
        );
        \wp_register_script(
            'tiFyPartial-Navtabs',
            $this->appAssetUrl('/Partial/Navtabs/js/scripts.js'),
            ['jquery-ui-widget'],
            170704,
            true
        );
        \wp_localize_script(
            'tiFyPartial-Navtabs',
            'tiFyPartialNavtabs',
            [
                '_ajax_nonce' => wp_create_nonce('tiFyPartialNavTabs')
            ]
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyPartial-Navtabs');
        \wp_enqueue_script('tiFyPartial-Navtabs');
    }

    /**
     * Mise à jour de l'onglet courant via Ajax.
     *
     * @return \wp_send_json
     */
    public function wp_ajax()
    {
        check_ajax_referer('tiFyPartialNavTabs');

        // Bypass
        if (!$key = $this->appRequest('POST')->get('key')) :
            wp_die(0);
        endif;

        $raw_key = base64_decode($key);
        if (!$raw_key = maybe_unserialize($raw_key)) :
            wp_die(0);
        else :
            $raw_key = maybe_unserialize($raw_key);
        endif;

        $success = \update_user_meta(get_current_user_id(), 'navtab' . $raw_key['_screen_id'], $raw_key['name']);

        \wp_send_json(['success' => $success]);
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->has('attrs.id')) :
            $this->set(
                'attrs.id',
                'tiFyPartial-Navtabs--' . $this->getId()
            );
        endif;

        if (!$this->has('attrs.class')) :
            $this->set(
                'attrs.class',
                'tiFyPartial-Navtabs tiFyPartial-Navtabs--' . $this->getId()
            );
        endif;

        $this->set('attrs.aria-control', 'navtabs');
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        return $this->appTemplateRender(
            'navtabs',
            [
                'html_attrs' => Tools::Html()->parseAttrs($this->get('attrs', [])),
                'items'      => Walker::display(
                    $this->get('nodes', []),
                    $this->get('options')
                )
            ]
        );
    }
}