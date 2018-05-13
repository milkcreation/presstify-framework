<?php

namespace tiFy\Form;

use tiFy\Apps\AppController;
use tiFy\Form\CommonDependencyAwareTrait;
use tiFy\Form\Forms\FormItemController;

abstract class AbstractCommonDependency extends AppController
{
    use CommonDependencyAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param FormItemController $Form Classe de rappel du controleur de formulaire associé.
     *
     * @return void
     */
    public function __construct(FormItemController $form)
    {
        $this->setForm($form);
    }
}