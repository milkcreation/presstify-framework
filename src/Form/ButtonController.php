<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\Button as ButtonInterface;
use tiFy\Contracts\Form\FormFactory as FormFactoryInterface;
use tiFy\Form\Factory\ResolverTrait;

abstract class ButtonController implements ButtonInterface
{
    use ResolverTrait;

    /**
     * Nom de qualification du bouton.
     * @var string
     */
    protected $name;

    /**
     * Attributs de configuration.
     * @var array
     */
    protected $attributes = [
        'label'           => '',
        'before'          => '',
        'after'           => '',
        'wrapper'         => true,
        'attrs'           => [],
        'order'           => 99,
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @void
     */
    public function __construct()
    {
        $this->name = $name;
        $this->form = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function make($name, $form, $attrs = [])
    {

        $this->attributes = $this->parse($attrs);

        if (method_exists($this, 'boot')) :
            call_user_func([$this, 'boot']);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        if (is_string($attrs)) :
            $attrs = ['label' => $attrs];
        endif;
        if (! Arr::get($attrs, 'attrs.class')) :
            ! Arr::set($attrs, 'attrs.class', 'tiFyForm-button tiFyForm-button--' . $this->getName());
        endif;

        return array_merge($this->attributes, $attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlAttrs()
    {
        return $this->parseHtmlAttrs($this->get('attrs'), false);
    }

    /**
     * {@inheritdoc}
     */
    public function displayHtmlAttrs()
    {
        return $this->parseHtmlAttrs($this->get('attrs'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if ($wrapper_attrs = $this->get('wrapper')) :
            if(! is_array($wrapper_attrs)) :
                $wrapper_attrs = [
                    'attrs' => [
                        'id'    => 'tiFyForm-buttonWrapper--' . $this->getForm()->getName(),
                        'class' => 'tiFyForm-buttonWrapper'
                    ]
                ];
            endif;

            $wrapper_attrs = array_merge(['tag' => 'div'], $wrapper_attrs);
            $wrapper_attrs['content'] = [$this, 'render'];

            $output = Partial::Tag($wrapper_attrs);
        else :
            $output = $this->render();
        endif;

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function render();
}