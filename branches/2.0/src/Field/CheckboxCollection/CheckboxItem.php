<?php

namespace tiFy\Field\CheckboxCollection;

use tiFy\Contracts\Field\FieldController;
use tiFy\Kernel\Parameters\AbstractParametersBag;

class CheckboxItem extends AbstractParametersBag
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
     * Nom de qualification
     * @var int|string
     */
    protected $name = '';

    /**
     * Instance du champ associé.
     * @var FieldController
     */
    protected $field;

    /**
     * CONSTRUCTEUR.
     *
     * @param string|int $name Nom de qualification.
     * @param array|string $attrs Liste des attributs de configuration.
     * @param FieldController $field Instance du contrôleur de champ associé.
     *
     * @return void
     */
    public function __construct($name, $attrs, FieldController $field)
    {
        $this->name = $name;
        $this->field = $field;
        $this->index = self::$_index++;

        if (is_string($attrs)) :
            $attrs = [
                'label' => [
                    'content' => $attrs
                ],
            ];
        endif;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'attrs'     => [],
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
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('attrs.id')) :
            $this->set('attrs.id', 'tiFyField-CheckboxCollectionItem--'. $this->index);
        endif;
        if (!$this->get('checkbox.name')) :
            $this->set('checkbox.name', $this->field->get('name'));
        endif;
        if (!$this->get('checkbox.checked')) :
            $this->set('checkbox.checked', $this->field->get('checked'));
        endif;
        if (!$this->get('checkbox.attrs.id')) :
            $this->set('checkbox.attrs.id', 'tiFyField-CheckboxCollectionItemInput--'. $this->index);
        endif;
        if (!$this->get('checkbox.attrs.class')) :
            $this->set('checkbox.attrs.class', 'tiFyField-CheckboxCollectionItemInput');
        endif;

        if (!$this->get('label.attrs.id')) :
            $this->set('label.attrs.id', 'tiFyField-CheckboxCollectionItemLabel--'. $this->index);
        endif;
        if (!$this->get('label.attrs.class')) :
            $this->set('label.attrs.class', 'tiFyField-CheckboxCollectionItemLabel');
        endif;
        if (!$this->get('label.attrs.for')) :
            $this->set('label.attrs.for', 'tiFyField-CheckboxCollectionItemInput--'. $this->index);
        endif;
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->field->viewer('item', $this->all())->render();
    }
}