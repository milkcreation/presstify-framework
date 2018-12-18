<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\ButtonController;
use tiFy\Contracts\Form\FactoryButtons;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Kernel\Collection\Collection;

class Buttons extends Collection implements FactoryButtons
{
    use ResolverTrait;

    /**
     * Liste des éléments associés au formulaire.
     * @var ButtonController[]
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
    public function __construct($buttons, FormFactory $form)
    {
        $this->form = $form;

        // Déclaration des champs.
        foreach($buttons as $name => $attrs) :
            if (is_numeric($name)) :
                $name = is_string($attrs) ? $attrs : null;
            endif;

            if (!is_null($name) && ($attrs !== false)) :
                $attrs = is_array($attrs) ? $attrs : [$attrs];

                if (app()->bound("form.button.{$name}")) :
                    $this->items[$name] = app()->singleton(
                        "form.factory.button.{$name}.{$this->form()->name()}",
                        function ($name, $attrs, FormFactory $form) {
                            return app()->resolve("form.button.{$name}", [$name, $attrs, $form]);
                        }
                    )->build([$name, $attrs, $this->form()]);
                else :
                    $this->items[$name] = app()->singleton(
                        "form.factory.button.{$name}.{$this->form()->name()}",
                        function ($name, $attrs, FormFactory $form) {
                            return app()->resolve("form.button", [$name, $attrs, $form]);
                        }
                    )->build([$name, $attrs, $this->form()]);
                endif;
            endif;
        endforeach;

        // ré-ordonnacement des boutons.
        $max = $this->collect()->max(
            function (ButtonController $button) {
                return $button->getPosition();
            }
        );
        if ($max) :
            $pad = 0;
            $this->collect()->each(
                function (ButtonController $button) use (&$pad, $max) {
                    $position = $button->getPosition() ? : ++$pad+$max;

                    return $button->set('position', absint($position));
                }
            );
        endif;

        $this->items = $this->byPosition();

        $this->events('buttons.init', [&$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function byPosition()
    {
        return $this->collect()->sortBy(function (ButtonController $button) {
            return $button->getPosition();
        })->all();
    }
}