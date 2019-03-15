<?php

namespace tiFy\Wp\Form;

use tiFy\Contracts\Form\FormManager;
use tiFy\Contracts\Wp\Form as FormContract;

class Form implements FormContract
{
    /**
     * Instance du controleur de gestion des formulaires.
     * @var FormManager
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param FormManager $manager Instance du controleur de gestion des formulaires.
     *
     * @return void
     */
    public function __construct(FormManager $manager)
    {
        $this->manager = $manager;

        add_action('init', function () {
            foreach (config('form', []) as $name => $attrs) :
                $this->manager->register($name, $attrs);
            endforeach;
        }, 999999);

        add_action('wp', function () {
            foreach ($this->manager->all() as $form) :
                $this->manager->current($form);
                $this->manager->reset();
            endforeach;
        }, 999999);
    }
}