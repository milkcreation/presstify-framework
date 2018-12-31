<?php

namespace tiFy\Field\Fields\SelectImage;

use tiFy\Contracts\Field\SelectChoice;
use tiFy\Field\FieldController;

class SelectImage extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     *      @var string|string[]|array|SelectChoice[]|SelectImageChoices $choices Chemin absolu vers les éléments de la
     *                                                                            liste de selection|Liste de selection
     *                                                                            d'éléments.
     * }
     */
    protected $attributes = [
        'before'     => '',
        'after'      => '',
        'name'       => '',
        'value'      => null,
        'attrs'      => [],
        'viewer'     => [],
        'choices'    => []
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

        $choices = $this->get('choices');
        if (!$choices instanceof SelectImageChoices) :
            $choices = new SelectImageChoices($choices, $this->viewer(), $this->getValue());
        endif;

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