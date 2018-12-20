<?php

namespace tiFy\Field\RadioCollection;

use tiFy\Contracts\Field\RadioChoice as RadioChoiceContract;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Field\Label\Label;
use tiFy\Field\Radio\Radio;

class RadioChoice extends ParamsBag implements RadioChoiceContract
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
     * Instance du bouton radio.
     * @var Radio
     */
    protected $radio;

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

        if ($attrs instanceof Radio) :
            $this->radio = $attrs;
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
            'radio'  => [
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
    public function getName()
    {
        return $this->getRadio() instanceof Radio ? $this->getRadio()->getName() : '';
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
    public function getRadio()
    {
        return $this->radio;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getRadio() instanceof Radio ? $this->getRadio()->getValue() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function isChecked()
    {
        return $this->getRadio() instanceof Radio ? in_array('checked', $this->getRadio()->get('attrs', [])) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('attrs.id')) :
            $this->set('attrs.id', 'FieldRadioCollection-item--'. $this->index);
        endif;

        if (!$this->get('radio.attrs.id')) :
            $this->set('radio.attrs.id', 'FieldRadioCollection-itemInput--'. $this->index);
        endif;

        if (!$this->get('radio.attrs.class')) :
            $this->set('radio.attrs.class', 'FieldRadioCollection-itemInput');
        endif;

        if (!$this->get('label.attrs.id')) :
            $this->set('label.attrs.id', 'FieldRadioCollection-itemLabel--'. $this->index);
        endif;

        if (!$this->get('label.attrs.class')) :
            $this->set('label.attrs.class', 'FieldRadioCollection-itemLabel');
        endif;

        if (!$this->get('label.attrs.for')) :
            $this->set('label.attrs.for', 'FieldRadioCollection-itemInput--'. $this->index);
        endif;

        $this->radio = field('radio', $this->get('radio', []));
        $this->label = field('label', $this->get('label', []));
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->getRadio() . $this->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        if($this->getRadio() instanceof Radio) :
            $this->getRadio()->set('attrs.name', $name);
        endif;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setChecked()
    {
        if($this->getRadio() instanceof Radio) :
            $this->getRadio()->push('attrs', 'checked');
        endif;

        return $this;
    }
}