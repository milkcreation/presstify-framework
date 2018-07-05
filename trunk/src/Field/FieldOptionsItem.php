<?php

namespace tiFy\Field;

use ArrayObject;
use tiFy\Kernel\Item\AbstractItemIterator;

class FieldOptionsItem extends AbstractItemIterator
{
    /**
     * Nom de qualification
     * @var int|string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des paramètres personnalisés.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name'    => $this->name,
            'group'   => false,
            'attrs'   => [],
            'parent'  => '',
            'value'   => $this->name,
            'content' => $this->name
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if ($this->get('group')) :
            $this->pull('value'); $this->pull('content');
            $this->set('attrs.label', $this->name);
        endif;
    }
}