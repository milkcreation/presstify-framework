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
        'choices'   => [],
        'choices_cb'   => SelectImageChoices::class,
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

        $this->set('attrs.class', trim($this->get('attrs.class') . ' FieldSelectImage'));

        $choices_cb = $this->get('choices_cb');
        $this->set('choices', new $choices_cb($this->get('choices', []), $this->viewer(), $this->getValue()));
    }

    /**
     * {@inheritdoc}
     */
    protected function parseDefaults()
    {
        $this->parseName();
        $this->parseValue();

        foreach($this->get('view', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
    }
}