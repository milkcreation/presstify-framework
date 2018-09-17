<?php

namespace tiFy\Metabox\Tab;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use tiFy\Contracts\Metabox\MetaboxItemInterface;
use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\Metabox\Metabox;
use tiFy\Partial\Partial;

class MetaboxTabDisplay
{
    /**
     * Liste des éléments à afficher.
     * @var MetaboxItemInterface[]
     */
    protected $items = [];

    /**
     * Instance de l'écran d'affichage courant.
     * @var WpScreenInterface
     */
    protected $screen;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct(WpScreenInterface $screen, Metabox $metabox)
    {
        $this->screen = $screen;

        $this->items = $metabox->getItems()
            ->filter(
                function ($item) {
                    /** @var MetaboxItemController $item */
                    return $item->getContext() === 'tab' &&
                        $item->isActive();
                }
            )
            ->sortBy(function ($item) {
                /** @var MetaboxItemController $item */
                return $item->getPosition();
            })
            ->all();

        if ($this->items) :
            switch($this->screen->getObjectType()) :
                case 'post_type' :
                    if ($this->screen->getObjectName() === 'page') :
                        add_action('edit_page_form', [$this, 'render']);
                    else :
                        add_action('edit_form_advanced', [$this, 'render']);
                    endif;
                    break;
                case 'options' :
                    add_settings_section('navtab', null, [$this, 'render'], $this->screen->getObjectName());
                    break;
                case 'taxonomy' :
                    add_action($this->screen->getObjectName() . '_edit_form', [$this, 'render'], 10, 2);
                    break;
                case 'user' :
                    add_action('show_user_profile', [$this, 'render']);
                    add_action('edit_user_profile', [$this, 'render']);
                    break;
            endswitch;

            add_action(
                'admin_enqueue_scripts',
                function () {
                    \wp_enqueue_style(
                        'MetaboxTab',
                        assets()->url('/metabox/tab/css/styles.css'),
                        ['PartialNavtabs'],
                        150216
                    );
                    \wp_enqueue_script(
                        'MetaboxTab',
                        assets()->url('/metabox/tab/js/scripts.js'),
                        ['PartialNavtabs'],
                        151019,
                        true
                    );
                }
            );
        endif;
    }

    /**
     * Traitement de la liste des onglets de la boîte de saisie.
     *
     * @return array
     */
    protected function parseItems()
    {
        $items = [];

        /* @todo
            $key_datas = ['name' => $item['name'], '_screen_id' => $this->screen->id];
            $key = base64_encode(serialize($key_datas));
            $current = ($this->current === $item['name']) ? true : false;

               data-key=\"{$key}\"
         */

        foreach($this->items as $item) :
            $items[] = [
                'name'      => $item->getName(),
                'title'     => $item->getTitle(),
                'parent'    => $item->getParent(),
                'content'   => $item->getContent(),
                'args'      => array_merge(func_get_args(), [$item->getArgs()]),
                'position'  => $item->getPosition(),
                // @todo 'current'   => (get_user_meta(get_current_user_id(), 'navtab' . get_current_screen()->id, true) === $node->getName())
            ];
        endforeach;

        return $items;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function render()
    {
        $args = func_num_args() && ($this->screen->getObjectType() !== 'options')
            ? func_get_args()
            : [];

        echo view()
            ->setDirectory(__DIR__ . '/views')
            ->render(
                'display',
                [
                    'title' => __('Réglages', 'tify'),
                    'items' => $this->parseItems()
                ]
            );
    }
}