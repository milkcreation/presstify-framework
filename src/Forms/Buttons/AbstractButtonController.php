<?php

namespace tiFy\Forms\Buttons;

use Illuminate\Support\Arr;
use tiFy\Apps\AppTrait;
use tiFy\Forms\Form\Form;

abstract class AbstractButtonController implements ButtonControllerInterface
{
    use AppTrait;

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
        'container_id'    => '',
        'container_class' => '',
        'class'           => '',
        'order'           => 99,
    ];

    /**
     * Classe de rappel du formulaire associé.
     * @var Form
     */
    protected $form;

    /**
     * {@inheritdoc}
     */
    public function make($name, $form, $attrs = [])
    {
        $this->name = $name;
        $this->form = $form;
        $this->attributes = $this->parse($attrs);
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
    public function form()
    {
        return $this->form;
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

        return array_merge($this->attributes, $attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        $classes = $this->get('class', '');

        $classes = is_string($classes)
            ? array_map('trim', explode(' ', $classes))
            : (array) $classes;

        $classes[] = "tiFyForm-button";
        $classes[] = "tiFyForm-button--" . $this->getName();

        return $this->form()->factory()->buttonClasses($this, $classes);
    }
    
    /**
     * {@inheritdoc}
     */
    public function display()
    {
        $openId = $this->get('container_id', '');
        $openClass = "tiFyForm-buttonWrapper tiFyForm-buttonWrapper--" . $this->getName();
        if ($container_class = $this->get('container_class', '')) :
            $openClass .= ' ' . $container_class;
        endif;

        $output = "";
        $output .= $this->form()->factory()->buttonOpen($this, $openId, $openClass);
        $output .= $this->render();
        $output .= $this->form()->factory()->buttonClose($this);

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function render();
}