<?php

namespace tiFy\Components\Partial\SlickCarousel;

use tiFy\Components\Partial\SlickCarousel\SlickCarouselWalker;
use tiFy\Partial\AbstractPartialController;
use tiFy\Kernel\Tools;

class SlickCarousel extends AbstractPartialController
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
            'tiFyPartialSlickCarousel',
            $this->appAsset('/Partial/SlickCarousel/css/styles.css'),
            ['slick', 'slick-theme'],
            170722
        );
        \wp_register_script(
            'tiFyPartialSlickCarousel',
            $this->appAsset('/Partial/SlickCarousel/js/scripts.js'),
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
        \wp_enqueue_style('tiFyPartialSlickCarousel');
        \wp_enqueue_script('tiFyPartialSlickCarousel');
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
        $this->set('attrs.id', 'tiFyPartial-SlickCarousel--' . $this->getId());
        $this->set('attrs.class', 'tiFyPartial-SlickCarousel tiFyPartial-SlickCarousel--' . $this->getId());

        $this->set(
            'items',
            [
                ['content' => '<img src="https://fr.facebookbrand.com/wp-content/uploads/2016/05/FB-fLogo-Blue-broadcast-2.png" />'],
                ['content' => '<img src="https://fr.facebookbrand.com/wp-content/uploads/2016/05/YES-ThumbFinal_4.9.15-2.png" />']
            ]
        );

        parent::parse($attrs);

        $this->set('attrs.aria-control', 'slick_carousel');
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
            'slick-carousel',
            [
                'items'         => SlickCarouselWalker::display(
                    $this->get('items', []),
                    [
                        'prefix' => 'tiFyPartial-SlickCarousel'
                    ]
                ),
                'html_attrs'    => Tools::Html()->parseAttrs($this->get('attrs', []))
            ]
        );
    }
}