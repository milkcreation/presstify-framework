<?php

namespace tiFy\Components\Forms\FieldTypes\Html;

use tiFy\Form\Fields\AbstractFieldTypeController;

class Html extends AbstractFieldTypeController
{
    /**
     * Liste des propriétés de formulaire supportées.
     * @var array
     */
    protected $support = ['wrapper'];

    /**
     * Rendu d'affichage du champ.
     *
     * @return string
     */
    public function render()
    {
        return $this->relField()->get('value');
    }
}