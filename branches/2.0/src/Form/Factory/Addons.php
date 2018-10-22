<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\AddonFactory;
use tiFy\Contracts\Form\FactoryAddons;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Factory\AbstractItemsIterator;
use tiFy\Form\Factory\ResolverTrait as FormFactoryResolver;

class Addons extends AbstractItemsIterator implements FactoryAddons
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

            if (!is_null($name) && ($attrs !== false)) :
                $attrs = is_array($attrs) ? $attrs : [$attrs];

                if (app()->bound("form.addon.{$name}")) :
                    $this->items[$name] = app()->singleton(
                        "form.factory.addon.{$name}.{$this->form()->name()}",
                        function ($name, $attrs, FormFactory $form) {
                            return app()->resolve("form.addon.{$name}", [$attrs, $form]);
                        }
                    )->build([$name, $attrs, $this->form()]);
                else :
                    $this->items[$name] = app()->singleton(
                        "form.factory.addon.{$name}.{$this->form()->name()}",
                        function ($name, $attrs, FormFactory $form) {
                            return app()->resolve("form.addon", [$name, $attrs, $form]);
                        }
                    )->build([$name, $attrs, $this->form()]);
                endif;
            endif;
        endforeach;

        $this->events('addons.init', [&$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }
}