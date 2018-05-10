<?php

namespace tiFy\Components\Forms\FieldTypes\Native;

use tiFy\Field\Field;
use tiFy\Form\Fields\AbstractFieldTypeController;

class Native extends AbstractFieldTypeController
{
    /**
     * Controleur d'affichage.
     * @var string
     */
    protected $fieldName;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($name, $support)
    {
        $this->fieldName = $name;
        $this->support = $support;
    }

    /**
     * Rendu d'affichage du champ.
     *
     * @return string
     */
    public function render()
    {
        return call_user_func(
            Field::class . "::{$this->fieldName}",
            [
                'name'  => $this->relField()->getName(),
                'value' => $this->relField()->getValue(),
                'attrs' => $this->relField()->getHtmlAttrs()
            ]
        );
    }
}