<?php

namespace tiFy\Field\Fields\Select;

use tiFy\Field\FieldController;

class Select extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string|array $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var bool $multiple Activation de la liste de selection multiple.
     *      @var string[]|array|SelectChoice[]|SelectChoices[] $choices Liste de selection d'éléments.
     * }
     */
    protected $attributes = [
        'before'   => '',
        'after'    => '',
        'attrs'    => [],
        'name'     => '',
        'value'    => null,
        'multiple' => false,
        'choices'  => []
    ];

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $value = $this->get('value', null);

        if (is_null($value)) :
            return null;
        endif;

        if (!is_array($value)) :
            $value = array_map('trim', explode(',', (string)$value));
        endif;

        $value = array_unique($value);

        if (!$this->get('multiple')) :
            $value = [reset($value)];
        endif;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $choices = $this->get('choices', []);
        if (!$choices instanceof SelectChoices) :
            $this->set('choices', new SelectChoices($choices, $this->getValue()));
        endif;

        if ($this->get('multiple')) :
            $this->push('attrs', 'multiple');
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function parseName()
    {
        if ($name = $this->get('name')) :
            $this->set(
                'attrs.name',
                $this->get('multiple')
                    ? "{$name}[]" :
                    $name
            );
        endif;
    }
}