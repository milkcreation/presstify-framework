<?php

namespace tiFy\Partial\Partials\Accordion;

use tiFy\Partial\PartialController;

class Accordion extends PartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'attrs'     => [],
        'items'     => [],
        'theme'     => 'light',
        // Définition de la liste des éléments ouvert à l'initialisation.
        'opened'    => null,
        // Activation de l'ouverture multiple d'élément frères
        'multiple'  => false,
        // Limite l'action d'ouverture et fermeture au déclencheur
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
                    assets()->url('partial/accordion/js/scripts.css'),
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
            $this->set('items', new AccordionItems($items, $this->viewer(), $this->get('selected')));
        endif;
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