<?php

namespace tiFy\Field\Radio;

use tiFy\Field\FieldController;

class Radio extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string $value Attribut de configuration de la valeur de soumission du champ "value" si l'élément est selectionné.
     *      @var null|string $checked Valeur de la selection.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'name'    => '',
        'value'   => '',
        'checked' => null
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.type', 'radio');

        if ($this->isChecked()) :
            $this->set('attrs.checked', 'checked');
        endif;
    }
}