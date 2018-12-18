<?php

namespace tiFy\Form\Field\Html;

use tiFy\Form\FieldController;

class Html extends FieldController
{
    /**
     * Liste des propriétés de formulaire supportées.
     * @var array
     */
    protected $supports = [];

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->field()->getValue();
    }
}