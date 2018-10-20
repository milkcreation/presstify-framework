<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\ButtonFactory;
use tiFy\Contracts\Form\FactoryButtons;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Factory\ResolverTrait as FormFactoryResolver;

class Buttons implements FactoryButtons
{
    use FormFactoryResolver;

    /**
     * Liste des éléments associés au formulaire.
     * @var ButtonFactory[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array Liste des boutons associés au formulaire.
     * @param FormFactory $form Instance du contrôleur de formulaire.
     *
     * @return void
     */
    public function __construct($buttons = [], FormFactory $form)
    {
        $this->form = $form;

        foreach($buttons as $name => $attrs) :
            if (is_numeric($name)) :
                $name = is_string($attrs) ? $attrs : null;
            endif;

            if (!is_null($name) && ($attrs !== false) && (app()->bound("form.button.{$name}"))) :
                $this->items[$name] = app()->singleton(
                    "form.factory.button.{$this->form->name()}.{$name}",
                    function () use ($name) {
                        return app()->resolve("form.button.{$name}", [$this->form]);
                    }
                )->build();
            endif;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }
}