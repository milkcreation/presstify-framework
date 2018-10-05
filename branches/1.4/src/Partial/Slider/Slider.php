<?php

namespace tiFy\Partial\Slider;

use tiFy\Partial\AbstractPartialItem;

class Slider extends AbstractPartialItem
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
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                \wp_register_style(
                    'PartialSlider',
                    assets()->url('partial/slider/css/styles.css'),
                    ['slick', 'slick-theme'],
                    170722
                );

                \wp_register_script(
                    'PartialSlider',
                    assets()->url('partial/slider/js/scripts.js'),
                    ['slick'],
                    170722,
                    true
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('PartialSlider');
        \wp_enqueue_script('PartialSlider');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->set('attrs.id', 'tiFyPartial-Slider--' . $this->getId());
        $this->set('attrs.class', 'tiFyPartial-Slider tiFyPartial-Slider--' . $this->getId());

        $this->set(
            'items',
            [
                /** @see https://picsum.photos/images */
                "<img src=\"https://picsum.photos/800/800/?image=768\" />",
                "<img src=\"https://picsum.photos/800/800/?image=669\" />",
                "<img src=\"https://picsum.photos/800/800/?image=646\" />",
                "<img src=\"https://picsum.photos/800/800/?image=883\" />"
            ]
        );

        parent::parse($attrs);

        $items = $this->get('items', []);
        foreach($items as &$item) :
            if (is_string($item)) :
                $item = ['content' => $item];
            endif;
            $item['tag'] = 'div';
        endforeach;
        $this->set('items', $items);

        $this->set('attrs.aria-control', 'slider');
        $this->set('attrs.data-slick', htmlentities(json_encode($this->get('options', []))));
    }
}