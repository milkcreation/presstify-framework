<?php

namespace tiFy\Field\Fields\Checkbox;

use tiFy\Contracts\Field\Checkbox as CheckboxContract;
use tiFy\Field\FieldController;

class Checkbox extends FieldController implements CheckboxContract
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
     *      @var null|bool $checked Activation de la selection.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'name'    => '',
        'value'   => '',
        'attrs'   => [],
        'viewer'  => [],
        'checked' => false,
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.type', 'checkbox');

        if ($this->isChecked()) :
            $this->set('attrs.checked', 'checked');
        endif;
    }
}