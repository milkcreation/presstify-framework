<?php

namespace tiFy\Partial\Partials\Accordion;

use tiFy\Contracts\Partial\Accordion as AccordionContract;
use tiFy\Partial\PartialController;

class Accordion extends PartialController implements AccordionContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var string $theme Theme d'affichage. light|dark.
     *      @var array|AccordionItem[]|AccordionItems Liste des éléments.
     *      @var mixed $opened Définition de la liste des éléments ouverts à l'initialisation.
     *      @var boolean $multiple Activation de l'ouverture multiple d'éléments.
     *      @var boolean $triggered Activation de la limite d'ouverture et de fermeture par le déclencheur de l'élement.
     * }
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'attrs'     => [],
        'viewer'    => [],
        'theme'     => 'light',
        'items'     => [],
        'opened'    => null,
        'multiple'  => false,
        'triggered' => false,
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                wp_register_style(
                    'PartialAccordion',
                    assets()->url('partial/accordion/css/styles.css'),
                    [],
                    181221
                );
                wp_register_script(
                    'PartialAccordion',
                    assets()->url('partial/accordion/js/scripts.js'),
                    ['jquery-ui-widget'],
                    181221,
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
        wp_enqueue_style('PartialAccordion');
        wp_enqueue_script('PartialAccordion');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.class', sprintf($this->get('attrs.class', '%s'), 'PartialAccordion'));

        if ($theme = $this->get('theme')) :
            $this->set('attrs.class', trim($this->get('attrs.class') . " PartialAccordion--{$theme}"));
        endif;

        $this->set('attrs.data-control', 'accordion');

        $this->set('attrs.data-id', $this->getId());

        $this->set(
            'attrs.data-options',
            [
                'multiple' => $this->get('multiple'),
                'opened' => $this->get('opened'),
                'triggered' => $this->get('triggered'),
            ]
        );

        $items = $this->get('items', []);
        if (!$items instanceof AccordionItems) :
            $items = new AccordionItems($items, $this->get('opened'));
        endif;

        $items->setPartial($this);

        $this->set('items', $items);
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaults()
    {
        foreach($this->get('view', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
    }
}