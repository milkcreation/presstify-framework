<?php

namespace tiFy\Field\CheckboxCollection;

use tiFy\Contracts\Field\CheckboxChoice as CheckboxChoiceContract;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Field\Label\Label;
use tiFy\Field\Checkbox\Checkbox;

class CheckboxChoice extends ParamsBag implements CheckboxChoiceContract
{
    /**
     * Compteur d'indice.
     * @var integer
     */
    static $_index = 0;

    /**
     * Indice de qualification.
     * @var integer
     */
    protected $index = 0;

    /**
     * Instance de l'intitulé.
     * @var Label
     */
    protected $label;

    /**
     * Nom de qualification.
     * @var int|string
     */
    protected $name = '';

    /**
     * Instance de la case à cocher.
     * @var Checkbox
     */
    protected $checkbox;

    /**
     * CONSTRUCTEUR.
     *
     * @param string|int $name Nom de qualification.
     * @param array|string $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs)
    {
        $this->name = $name;
        $this->index = self::$_index++;

        if (is_string($attrs)) :
            $attrs = [
                'label' => [
                    'content' => $attrs
                ],
            ];
        endif;

        if ($attrs instanceof Checkbox) :
            $this->checkbox = $attrs;
        else :
            parent::__construct($attrs);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'label'     => [
                'before'       => '',
                'after'        => '',
                'content'      => '',
                'attrs'        => []
            ],
            'checkbox'  => [
                'before'  => '',
                'after'   => '',
                'attrs'   => [],
                'name'    => '',
                'value'   => $this->name,
                'checked' => null
            ]

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckbox()
    {
        return $this->checkbox;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getCheckbox() instanceof Checkbox ? $this->getCheckbox()->getName() : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getCheckbox() instanceof Checkbox ? $this->getCheckbox()->getValue() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function isChecked()
    {
        return $this->getCheckbox() instanceof Checkbox
            ? in_array('checked', $this->getCheckbox()->get('attrs', [])) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('attrs.id')) :
            $this->set('attrs.id', 'FieldCheckboxCollection-item--'. $this->index);
        endif;

        if (!$this->get('checkbox.attrs.id')) :
            $this->set('checkbox.attrs.id', 'FieldCheckboxCollection-itemInput--'. $this->index);
        endif;

        if (!$this->get('checkbox.attrs.class')) :
            $this->set('checkbox.attrs.class', 'FieldCheckboxCollection-itemInput');
        endif;

        if (!$this->get('label.attrs.id')) :
            $this->set('label.attrs.id', 'FieldCheckboxCollection-itemLabel--'. $this->index);
        endif;

        if (!$this->get('label.attrs.class')) :
            $this->set('label.attrs.class', 'FieldCheckboxCollection-itemLabel');
        endif;

        if (!$this->get('label.attrs.for')) :
            $this->set('label.attrs.for', 'FieldCheckboxCollection-itemInput--'. $this->index);
        endif;

        $this->checkbox = field('checkbox', $this->get('checkbox', []));
        $this->label = field('label', $this->get('label', []));
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->getCheckbox() . $this->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        if($this->getCheckbox() instanceof Checkbox) :
            $this->getCheckbox()->set('attrs.name', $name);
        endif;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setChecked()
    {
        if($this->getCheckbox() instanceof Checkbox) :
            $this->getCheckbox()->push('attrs', 'checked');
        endif;

        return $this;
    }
}