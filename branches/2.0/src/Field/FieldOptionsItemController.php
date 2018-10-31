<?php

namespace tiFy\Field;

use tiFy\Kernel\Params\ParamsBag;

class FieldOptionsItemController extends ParamsBag
{
    /**
     * Indice de qualification.
     * @var int|string
     */
    protected $index = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param int $index Indice de qualification.
     * @param array $attrs Liste des paramètres personnalisés.
     *
     * @return void
     */
    public function __construct($index, $attrs = [])
    {
        $this->index = $index;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name'      => (string) $this->index,
            'group'     => false,
            'attrs'     => [],
            'parent'    => '',
            'value'     => $this->index,
            'content'   => ''
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return (string)htmlentities($this->get('label'));
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return (string)$this->get('value');
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return !empty($this->get('parent'));
    }

    /**
     * {@inheritdoc}
     */
    public function isDisabled()
    {
        return in_array('disabled', $this->get('attrs', []));
    }

    /**
     * {@inheritdoc}
     */
    public function isGroup()
    {
        return $this->get('group');
    }

    /**
     * {@inheritdoc}
     */
    public function isSelected()
    {
        return in_array('selected', $this->get('attrs', []));
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if ($this->get('group')) :
            $this->pull('value');
            $this->set('attrs.label', $this->pull('content'));
        endif;
    }
}