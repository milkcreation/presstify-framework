<?php

namespace tiFy\Form\Field\Html;

use tiFy\Contracts\Form\FactoryField;
use tiFy\Form\FieldController;

class Html extends FieldController
{
    /**
     * Liste des propriétés de formulaire supportées.
     * @var array
     */
    protected $supports = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param FactoryField $field Instance du contrôleur de champ de formulaire associé.
     *
     * @void
     */
    public function __construct(FactoryField $field)
    {
        parent::__construct('html', $field);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->field()->getValue();
    }
}