<?php

namespace tiFy\Components\Partial\HolderImage;

use tiFy\Partial\AbstractPartialController;
use tiFy\Kernel\Tools;

class HolderImage extends AbstractPartialController
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
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyPartialHolderImage',
            $this->appAssetUrl('/Partial/HolderImage/css/styles.css'),
            [],
            160714
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyPartialHolderImage');
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        $this->attributes['attrs']['id'] = 'tiFyPartial-HolderImage--' . $this->getIndex();
        $this->attributes['attrs']['class'] = "tiFyPartial-HolderImage tiFyPartial-HolderImage--" . $this->getId();
        $this->attributes['content'] = "<span class=\"tiFyPartial-HolderImageContentDefault\">{$this->get('width')}x{$this->get('height')}</span>";

        parent::parse($attrs);

        $this->set('attrs.aria-control', 'holder_image');
        $this->set('attrs.style', "background-color:{$this->get('background-color')};color:{$this->get('foreground-color')};font-size:{$this->get('font-size')}\"");

        $this->set('_attrs', Tools::Html()->parseAttrs($this->get('attrs')));
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        return $this->appTemplateRender('holder-image', $this->all());
    }
}