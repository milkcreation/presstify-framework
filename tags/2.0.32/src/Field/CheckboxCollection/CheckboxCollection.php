<?php

namespace tiFy\Field\CheckboxCollection;

use tiFy\Field\FieldController;
use tiFy\Field\Checkbox\Checkbox;

class CheckboxCollection extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var array|Checkbox[]|CheckboxChoice[]|CheckboxChoices $choices
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
        if (!$choices instanceof CheckboxChoices) :
            $this->set('choices', new CheckboxChoices($choices, $this->getName(), $this->viewer(), $this->getValue()));
        endif;
    }

    /**
     * Traitement de l'attribut de configuration de la clé d'indexe de soumission du champ "name".
     *
     * @return void
     */
    protected function parseName()
    {
        if ($name = $this->get('name')) :
            $this->set('attrs.name', "{$name}[]");
        endif;
    }
}