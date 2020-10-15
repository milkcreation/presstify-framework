<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FieldController as FieldControllerContract;
use tiFy\Form\Factory\ResolverTrait;

class FieldController implements FieldControllerContract
{
    use ResolverTrait;

    /**
     * Liste des attributs de support des types de champs natifs.
     * @var array
     */
    protected $fieldSupports = [
        'button'              => ['request', 'wrapper'],
        'checkbox'            => ['checking', 'label', 'request', 'wrapper', 'session', 'tabindex', 'transport'],
        'checkbox-collection' => ['choices', 'label', 'request', 'session', 'tabindexes', 'transport', 'wrapper'],
        'datetime-js'         => ['label', 'request', 'session', 'tabindexes', 'transport', 'wrapper'],
        'file'                => ['label', 'request', 'tabindex', 'wrapper'],
        'hidden'              => ['request', 'session', 'transport'],
        'label'               => ['wrapper'],
        'password'            => ['label', 'request', 'tabindex', 'wrapper'],
        'radio'               => ['label', 'request', 'session', 'tabindex', 'transport', 'wrapper'],
        'radio-collection'    => ['choices', 'label', 'request', 'session', 'tabindexes', 'transport', 'wrapper'],
        'repeater'            => ['label', 'request', 'session', 'tabindexes', 'transport', 'wrapper'],
        'select'              => ['choices', 'label',  'request', 'session', 'tabindex', 'transport', 'wrapper'],
        'select-js'           => ['choices', 'label', 'request', 'session', 'tabindex', 'transport', 'wrapper'],
        'submit'              => ['request', 'tabindex', 'wrapper'],
        'toggle-switch'       => ['request', 'tabindex', 'session', 'transport', 'wrapper'],
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
    protected $supports = ['label', 'request', 'session', 'tabindex', 'transport', 'wrapper'];

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
        if (isset($this->fieldSupports[$this->field()->getType()])) {
            return $this->fieldSupports[$this->field()->getType()];
        } else {
            return $this->supports;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $args = array_merge($this->field()->getExtras(), [
            'name'  => $this->field()->getName(),
            'attrs' => $this->field()->get('attrs', [])
        ]);

        if($this->field()->supports('choices')) {
            $args['choices'] = $this->field()->get('choices', []);
        }

        $args['value'] = $this->field()->getValue();

        return field($this->field()->getType(), $args);
    }
}