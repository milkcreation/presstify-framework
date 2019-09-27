<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\Slideshow;

use tiFy\Metabox\MetaboxDriver;

class Slideshow extends MetaboxDriver
{
    /**
     * @inheritDoc
     */
    public function content(...$args): string
    {
        return 'tutu';
        return (string)$this->viewer('content', $this->all());
    }

    /**
     * @inheritDoc
     *
     * @return array {
     * @var string $name Nom de qualification d'enregistrement.
     * @var array $attrs Liste des attributs de balisae HTML du conteneur.
     * @var string $ajax_action Action Ajax de récupération des éléments.
     * @var array $editable Liste des interfaces d'édition des vignettes actives.
     * @var integer $max Nombre maximum de vignette.
     * @var array $args Liste des attribut de requête Ajax complémentaires.
     * @todo boolean|array $suggest Liste de selection de contenu.
     * @var boolean $custom Activation de l'ajout de vignettes personnalisées.
     * @var array $options Liste des options d'affichage.
     * @var array $viewer Liste des attributs de configuration du gestionnaire de gabarit.
     * @var string $item_class Traitement de l'affichage d'un élément
     * }
     */
    public function defaults(): array
    {
        return [
            'args'        => [],
            'attrs'       => [],
            'ajax_action' => 'metabox_options_slideshow',
            'custom'      => true,
            'editable'    => ['image', 'title', 'url', 'caption'],
            'item_class'  => SlideshowItem::class,
            'max'         => -1,
            'name'        => 'tify_taboox_slideshow',
            'options'     => [],
            'viewer'      => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function header($args = null, $null1 = null, $null2 = null)
    {
        return $this->item->getTitle() ?: __('Diaporama', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function parse()
    {
        parent::parse();

        $exists = array_merge(['options' => [], 'items' => []], get_option($this->get('name')) ?: []);

        $items = $exists['items'] ?? [];
        array_walk($items, function (&$attrs, $index) {
            $attrs['name'] = $this->get('name');
            $attrs['editable'] = $this->get('editable', []);
            $itemClass = $this->get('item_class', SlideshowItem::class);

            $attrs = new $itemClass($index, $attrs, $this->viewer());
        });

        $this->set([
            'attrs.class'        => 'MetaboxOptions-slideshow',
            'attrs.data-options' => array_merge($this->get('args', []), [
                'action'      => $this->get('ajax_action'),
                '_ajax_nonce' => wp_create_nonce('MetaboxOptionsSlideshow'),
                'editable'    => $this->get('editable'),
                'name'        => $this->get('name'),
                'max'         => $this->get('max'),
                'viewer'      => $this->get('viewer'),
                'item_class'  => $this->get('item_class'),
            ]),
            'items'              => $items,
            'options'            => array_merge([
                'ratio'       => '16:9',
                'size'        => 'full',
                'nav'         => true,
                'tab'         => true,
                'progressbar' => false,
            ], $exists['options'] ?? []),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function settings()
    {
        return [$this->get('name')];
    }

    /**
     * Action de récupération Ajax d'un élément.
     *
     * @return string
     */
    public function wp_ajax()
    {
        $attrs = [
            'post_id'   => request()->post('post_id'),
            'clickable' => request()->post('post_id') ? 1 : 0,
            'name'      => request()->post('name'),
            'editable'  => request()->post('editable', []),
        ];
        $itemClass = wp_unslash(request()->post('item_class', SlideshowItem::class));
        $this->viewer = null;
        $this->set('viewer', request()->post('viewer', []));

        echo new $itemClass(null, $attrs, $this->viewer());
        exit;
    }
}