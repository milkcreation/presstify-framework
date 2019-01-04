<?php

namespace tiFy\Field\Fields\Select;

use tiFy\Contracts\Field\Select as SelectContract;
use tiFy\Field\FieldController;

class Select extends FieldController implements SelectContract
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
     *      @var bool $multiple Activation de la liste de selection multiple.
     *      @var string[]|array|SelectChoice[]|SelectChoices $choices Liste de selection d'éléments.
     * }
     */
    protected $attributes = [
        'before'   => '',
        'after'    => '',
        'name'     => '',
        'value'    => null,
        'attrs'    => [],
        'viewer'   => [],
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
            $this->set('attrs.name', $this->get('multiple') ? "{$name}[]" : $name);
        endif;
    }
}