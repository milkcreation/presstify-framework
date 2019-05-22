<?php

namespace tiFy\Partial\Partials\Slider;

use tiFy\Contracts\Partial\Slider as SliderContract;
use tiFy\Partial\PartialFactory;

class Slider extends PartialFactory implements SliderContract
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('init', function () {
            wp_register_style(
                'PartialSlider',
                asset()->url('partial/slider/css/styles.css'),
                ['slick', 'slick-theme'],
                170722
            );

            wp_register_script(
                'PartialSlider',
                asset()->url('partial/slider/js/scripts.js'),
                ['slick'],
                170722,
                true
            );
        });
    }

    /**
     * Liste des attributs de configuration.
     *
     * @return array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var string[]|callable[] $items Liste des éléments. Liste de sources d'image|Liste de contenu HTML|Liste de
     *                                      fonctions. défaut : @see https://picsum.photos/images
     *      @var array $options Liste des attributs de configuration du pilote d'affichage.
     *                          @see http://kenwheeler.github.io/slick/#settings
     * }
     */
    public function defaults()
    {
        return [
            'before'  => '',
            'after'   => '',
            'attrs'   => [],
            'viewer'  => [],
            'items'   => [
                'https://picsum.photos/800/800/?image=768',
                'https://picsum.photos/800/800/?image=669',
                'https://picsum.photos/800/800/?image=646',
                'https://picsum.photos/800/800/?image=883',
            ],
            'options' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialSlider');
        wp_enqueue_script('PartialSlider');
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        $items = $this->get('items', []);

        $validator = validator();

        foreach($items as &$item) :
            if (is_callable($item)) :
                $item = call_user_func($item);
            elseif (is_array($item)) :
            elseif ($validator->isUrl($item)) :
                $item = "<img src=\"{$item}\" alt=\"\"/>";
            endif;
        endforeach;
        $this->set('items', $items);

        $this->set('attrs.aria-control', 'slider');
        $this->set('attrs.data-slick', htmlentities(json_encode($this->get('options', []))));
    }
}