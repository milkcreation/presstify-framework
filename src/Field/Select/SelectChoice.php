<?php

namespace tiFy\Field\Select;

use tiFy\Contracts\Field\SelectChoice as SelectChoiceContract;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Kernel\Tools;

class SelectChoice extends ParamsBag implements SelectChoiceContract
{
    /**
     * Nom de qualification.
     * @var int|string
     */
    protected $name = '';

    /**
     * Niveau de profondeur d'affichage dans le selecteur.
     * @var int
     */
    private $depth = 0;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param array|string $attrs Liste des attributs de configuration|Intitulé de qualification de l'option.
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
            'name'      => $this->name,
            'group'     => false,
            'attrs'     => [],
            'parent'    => null,
            'value'     => $this->name,
            'content'   => ''
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return (string)$this->get('content');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return (string)$this->get('name', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->get('value');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->get('parent', null);
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return !is_null($this->get('parent'));
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
        return !$this->isGroup() && in_array('selected', $this->get('attrs', []), true);
    }

    /**
     * {@inheritdoc}
     */
    public function setDepth($depth = 0)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if ($this->isGroup()) :
            $this->pull('value');
            $this->set('attrs.label', htmlentities($this->pull('content')));
        else :
            $this->set('attrs.value', $this->getValue());
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function tagClose()
    {
        return $this->isGroup() ? "</optgroup>" : "</option>";
    }

    /**
     * {@inheritdoc}
     */
    public function tagContent()
    {
        return $this->getContent() ? $this->getContent() : '';
    }

    /**
     * {@inheritdoc}
     */
    public function tagOpen()
    {
        $attrs = ($attrs = Tools::Html()->parseAttrs($this->get('attrs', []), true))
            ? " {$attrs}"
            : '';

        return "\n" . str_repeat("\t", $this->depth) . ($this->isGroup() ? "<optgroup{$attrs}>" : "<option{$attrs}>");
    }
}