<?php

namespace tiFy\Components\Partial\Slider;

use tiFy\Components\Partial\Slider\SliderWalker;
use tiFy\Partial\AbstractPartialController;
use tiFy\Kernel\Tools;

class Slider extends AbstractPartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *
     * }
     */
    protected $attributes = [
        'attrs'   => [],
        'items'   => [],
        // Options
        // @see http://kenwheeler.github.io/slick/#settings
        'options' => [],

    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyPartialSlider',
            $this->appAsset('/Partial/Slider/css/styles.css'),
            ['slick', 'slick-theme'],
            170722
        );
        \wp_register_script(
            'tiFyPartialSlider',
            $this->appAsset('/Partial/Slider/js/scripts.js'),
            ['slick'],
            170722,
            true
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyPartialSlider');
        \wp_enqueue_script('tiFyPartialSlider');
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
        $this->set('attrs.id', 'tiFyPartial-Slider--' . $this->getId());
        $this->set('attrs.class', 'tiFyPartial-Slider tiFyPartial-Slider--' . $this->getId());

        $this->set(
            'items',
            [
                ['content' => '<img src="https://fr.facebookbrand.com/wp-content/uploads/2016/05/FB-fLogo-Blue-broadcast-2.png" />'],
                ['content' => '<img src="https://fr.facebookbrand.com/wp-content/uploads/2016/05/YES-ThumbFinal_4.9.15-2.png" />']
            ]
        );

        parent::parse($attrs);

        $this->set('attrs.aria-control', 'slider');
        $this->set('attrs.data-slick', htmlentities(json_encode($this->get('options', []))));
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        return  $this->appTemplateRender(
            'slider',
            [
                'items'         => SliderWalker::display(
                    $this->get('items', []),
                    [
                        'prefix' => 'tiFyPartial-Slider'
                    ]
                ),
                'html_attrs'    => Tools::Html()->parseAttrs($this->get('attrs', []))
            ]
        );
    }
}