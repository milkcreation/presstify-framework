<?php

namespace tiFy\Form\Field;

use tiFy\Contracts\Form\FactoryField;
use tiFy\Form\FieldController;

class Defaults extends FieldController
{
    /**
     * Nom de qualification (type).
     * @var string
     */
    protected $name = '';

    /**
     * {@inheritdoc}
     */
    public function __construct($name, FactoryField $field)
    {
        $this->name = $name;

        parent::__construct($field);
    }

    /**
     * {@inheritdoc}
     */
    public function content()
    {
        return field(
            $this->field()->getType(),
            array_merge(
                $this->field()->getExtras(),
                [
                    'name'  => $this->field()->getName(),
                    'value' => $this->field()->getValue(),
                    'attrs' => $this->field()->get('attrs', [])
                ]
            )
        );
    }
}