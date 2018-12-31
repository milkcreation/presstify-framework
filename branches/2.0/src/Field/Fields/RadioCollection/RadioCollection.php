<?php

namespace tiFy\Field\Fields\RadioCollection;

use tiFy\Field\FieldController;
use tiFy\Field\Fields\Radio\Radio;

class RadioCollection extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du conteneur de champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     *      @var array|Radio[]|RadioChoice[]|RadioChoices $choices
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'name'    => '',
        'value'   => null,
        'attrs'   => [],
        'viewer'  => [],
        'choices' => []
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