<?php

namespace tiFy\Field\SelectImage;

use tiFy\Field\FieldController;

class SelectImage extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     * }
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'attrs'     => [],
        'name'      => '',
        'value'     => null,
        'choices'   => []
    ];

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('FieldSelectImage');
        wp_enqueue_script('FieldSelectJs');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        wp_register_style(
            'FieldSelectImage',
            assets()->url('field/select-image/css/styles.css'),
            ['FieldSelectJs'],
            180808
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $choices = $this->get('choices', []);
        if (!$choices instanceof SelectImageChoices) :
            $this->set('choices', new SelectImageChoices($choices, $this->viewer(), $this->getValue()));
        endif;
    }
}