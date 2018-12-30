<?php

namespace tiFy\Field\Fields\SelectImage;

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
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action('init', function () {
            wp_register_style(
                'FieldSelectImage',
                assets()->url('field/select-image/css/styles.css'),
                ['FieldSelectJs'],
                180808
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('FieldSelectImage');
        wp_enqueue_script('FieldSelectJs');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.class', trim($this->get('attrs.class', '%s') . ' FieldSelectJs FieldSelectImage'));

        $choices_cb = $this->get('choices_cb');
        $choices = new $choices_cb($this->get('choices', []), $this->viewer(), $this->getValue());
        $this->set('choices', $choices);
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