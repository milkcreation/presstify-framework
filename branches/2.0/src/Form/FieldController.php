<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Form\Field as FieldInterface;
use tiFy\Form\Factory\ResolverTrait;

abstract class FieldController implements FieldInterface
{
    use ResolverTrait;

    /**
     * Liste des attributs de support.
     * @var array
     */
    protected $supports = ['label', 'request', 'tabindex', 'wrapper'];

    /**
     * {@inheritdoc}
     */
    public function __construct(FactoryField $field)
    {
        $this->field = $field;
        $this->form = $field->form();

        $this->boot();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->content();
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
    public function form()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function supports()
    {
        return $this->supports;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function content();
}