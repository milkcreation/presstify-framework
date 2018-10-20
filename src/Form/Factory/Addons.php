<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\AddonFactory;
use tiFy\Contracts\Form\FactoryAddons;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Factory\ResolverTrait as FormFactoryResolver;

class Addons implements FactoryAddons
{
    use FormFactoryResolver;

    /**
     * Liste des éléments associés au formulaire.
     * @var AddonFactory[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $addons Liste des addons associés au formulaire.
     * @param FormFactory $form Instance du contrôleur de formulaire.
     *
     * @return void
     */
    public function __construct($addons = [], FormFactory $form)
    {
        $this->form = $form;

        foreach($addons as $name => $attrs) :
            if (is_numeric($name)) :
                $name = is_string($attrs) ? $attrs : null;
            endif;

            if (!is_null($name) && ($attrs !== false) && (app()->bound("form.addon.{$name}"))) :
                $this->items[$name] = app()->singleton(
                    "form.factory.addon.{$this->form->name()}.{$name}",
                    function () use ($name) {
                        return app()->resolve("form.addon.{$name}", [$this->form]);
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