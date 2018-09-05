<?php

namespace tiFy\Partial\HolderImage;

use tiFy\Partial\AbstractPartialItem;
use tiFy\Kernel\Tools;

class HolderImage extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     * }
     */
    protected $attributes = [
        'attrs'            => [],
        'content'          => '',
        'width'            => 100,
        'height'           => 100,
        'background-color' => '#E4E4E4',
        'foreground-color' => '#AAA',
        'font-size'        => '1em'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        app()->appAddAction(
            'init',
            function () {
                \wp_register_style(
                    'PartialHolderImage',
                    \assets()->url('/partial/holder-image/css/styles.css'),
                    [],
                    160714
                );
            }
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('PartialHolderImage');
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
        $this->attributes['attrs']['id'] = 'tiFyPartial-HolderImage--' . $this->getIndex();
        $this->attributes['attrs']['class'] = "tiFyPartial-HolderImage tiFyPartial-HolderImage--" . $this->getId();

        parent::parse($attrs);

        if (!$this->get('content')) :
            $this->set('content',
                "<span class=\"tiFyPartial-HolderImageContentDefault\">{$this->get('width')}x{$this->get('height')}</span>"
            );
        endif;

        $this->set('attrs.aria-control', 'holder_image');
        $this->set('attrs.style', "max-width:{$this->get('width')}px;max-height:{$this->get('height')}px;background-color:{$this->get('background-color')};color:{$this->get('foreground-color')};font-size:{$this->get('font-size')}\"");

        $this->set('_attrs', Tools::Html()->parseAttrs($this->get('attrs')));
    }
}