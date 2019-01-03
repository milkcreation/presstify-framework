<?php

namespace tiFy\Partial\Partials\Navtabs;

use tiFy\Contracts\Partial\Navtabs as NavtabsContract;
use tiFy\Partial\PartialController;

class Navtabs extends PartialController implements NavtabsContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var array $items {
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
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'viewer'  => [],
        'items'   => [],
        'options' => [
            'prefix' => 'tiFyPartial-Navtabs',
        ],
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
                    'wp_ajax_tify_partial_navtabs',
                    [$this, 'wp_ajax']
                );
                add_action(
                    'wp_ajax_nopriv_tify_partial_navtabs',
                    [$this, 'wp_ajax']
                );

                // Déclaration des scripts
                \wp_register_style(
                    'PartialNavtabs',
                    assets()->url('partial/navtabs/css/styles.css'),
                    [],
                    170704
                );
                \wp_register_script(
                    'PartialNavtabs',
                    assets()->url('partial/navtabs/js/scripts.js'),
                    ['jquery-ui-widget'],
                    170704,
                    true
                );
                \wp_localize_script(
                    'PartialNavtabs',
                    'tiFyPartialNavtabs',
                    [
                        '_ajax_nonce' => wp_create_nonce('tiFyPartialNavTabs')
                    ]
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        return $this->viewer(
            'navtabs',
            [
                'attrs'      => $this->get('attrs', []),
                'items'      => Walker::display(
                    $this->get('items', []),
                    $this->get('options')
                )
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialNavtabs');
        wp_enqueue_script('PartialNavtabs');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.aria-control', 'navtabs');
    }

    /**
     * {@inheritdoc}
     */
    public function wp_ajax()
    {
        check_ajax_referer('tiFyPartialNavTabs');

        if (!$key = request()->post('key')) :
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
}