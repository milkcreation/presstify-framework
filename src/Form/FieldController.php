<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FieldController as FieldControllerInterface;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Factory\ResolverTrait;

class FieldController implements FieldControllerInterface
{
    use ResolverTrait;

    /**
     * Liste des attributs de support des types de champs natifs.
     * @var array
     */
    protected $fieldSupports = [
        'button'              => ['request', 'wrapper'],
        'checkbox-collection' => ['label', 'request', 'tabindexes', 'wrapper'],
        'datetime-js'         => ['label', 'request', 'tabindexes', 'wrapper'],
        'hidden'              => ['request'],
        'label'               => ['wrapper'],
        'password'            => ['label', 'request', 'tabindex', 'wrapper'],
        'radio-collection'    => ['label', 'request', 'tabindexes', 'wrapper'],
        'repeater'            => ['label', 'request', 'tabindexes', 'wrapper'],
        'submit'              => ['request', 'tabindex', 'wrapper'],
        'toggle-switch'       => ['request', 'tabindex', 'wrapper'],
    ];

    /**
     * Nom de qualification (type).
     * @var string
     */
    protected $name = '';

    /**
     * Liste des propriétés de support.
     * @var array
     */
    protected $supports = ['label', 'request', 'tabindex', 'wrapper', 'transport'];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param FactoryField $field Instance du contrôleur de champ de formulaire associé.
     *
     * @void
     */
    public function __construct($name, FactoryField $field)
    {
        $this->name = $name;
        $this->field = $field;
        $this->form = $field->form();

        $this->boot();
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
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function supports()
    {
        if (isset($this->fieldSupports[$this->field()->getType()])) :
            return $this->fieldSupports[$this->field()->getType()];
        else :
            return $this->supports;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $args = array_merge(
            $this->field()->getExtras(),
            [
                'name'  => $this->field()->getName(),
                'value' => $this->field()->getValue(),
                'attrs' => $this->field()->get('attrs', [])
            ]
        );

        if (in_array($this->field()->getType(), ['checkbox', 'radio'])) :
            $args['checked'] = $this->field()->getValue();
        endif;

        return field($this->field()->getType(), $args);
    }
}