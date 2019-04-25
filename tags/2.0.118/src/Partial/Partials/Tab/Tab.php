<?php

namespace tiFy\Partial\Partials\Tab;

use tiFy\Contracts\Partial\Tab as TabContract;
use tiFy\Contracts\Partial\TabItems as TabItemsContract;
use tiFy\Partial\PartialFactory;

class Tab extends PartialFactory implements TabContract
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('init', function () {
            wp_register_style(
                'PartialTab',
                assets()->url('partial/tab/css/styles.css'),
                [],
                170704
            );

            wp_register_script(
                'PartialTab',
                assets()->url('partial/tab/js/scripts.js'),
                ['jquery-ui-widget'],
                170704,
                true
            );
        });
    }

    /**
     * Liste des attributs de configuration.
     *
     * @return array $attributes {
     *      @var string $active Nom de qualification de l'élément actif.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var string $before Contenu placé avant.
     *      @var array $items {
     *          Liste des onglets de navigation.
     *
     *          @var string $name Nom de qualification.
     *          @var string $parent Nom de qualification de l'élément parent.
     *          @var string|callable $content
     *          @var int $position Ordre d'affichage dans le
     *      }
     *      @var array $rotation Rotation des styles d'onglet.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     */
    public function defaults()
    {
        return [
            'active'   => null,
            'after'    => '',
            'attrs'    => [],
            'before'   => '',
            'items'    => [],
            'rotation' => [],
            'viewer'   => []
        ];
    }

    /**
     * @inheritdoc
     */
    public function display()
    {
        /* @var TabItemsContract $items */
        $items = $this->get('items');

        return (string)$this->viewer('tab', ['attrs' => $this->get('attrs', []), 'items' => $items->getGrouped()]);
    }

    /**
     * @inheritdoc
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialTab');
        wp_enqueue_script('PartialTab');
    }

    /**
     * @inheritdoc
     */
    public function getTabStyle(int $depth = 0)
    {
        return $this->get("rotation.{$depth}") ? : 'default';
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        $this->set('attrs.data-control', 'tab');

        $items = $this->get('items', []);
        if (!$items instanceof TabItemsContract) {
            $items = new TabItems($items, $this->get('active'));
        }
        /* @var TabItemsContract $items */
        $this->set('items', $items->prepare($this));
    }

    /**
     * @inheritdoc
     */
    public function viewer($view = null, $data = [])
    {
        if (is_null($this->viewer)) {
            $this->viewer = app()->get('partial.viewer', [$this]);
            $this->viewer->setController(TabView::class);
        }
        return parent::viewer($view, $data);
    }

    /**
     * @inheritdoc
     */
    public function wp_ajax()
    {
        check_ajax_referer('tiFyPartialTab');

        if (!$key = request()->post('key')) {
            wp_die(0);
        }

        $raw_key = base64_decode($key);
        if (!$raw_key = maybe_unserialize($raw_key)) {
            wp_die(0);
        } else {
            $raw_key = maybe_unserialize($raw_key);
        };

        $success = update_user_meta(get_current_user_id(), 'tab' . $raw_key['_screen_id'], $raw_key['name']);

        wp_send_json(['success' => $success]);
    }
}