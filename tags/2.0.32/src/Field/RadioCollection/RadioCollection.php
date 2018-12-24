<?php

namespace tiFy\Field\RadioCollection;

use tiFy\Field\FieldController;
use tiFy\Field\Radio\Radio;

class RadioCollection extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var array|Radio[]|RadioChoice[]|RadioChoices $choices
     *      @var null|string $value Valeur de la selection.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'name'    => '',
        'choices' => [],
        'value'   => null
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $choices = $this->get('choices', []);
        if (!$choices instanceof RadioChoices) :
            $this->set('choices', new RadioChoices($choices, $this->getName(), $this->viewer(), $this->getValue()));
        endif;
    }
}