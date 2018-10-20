<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\FormView as FormViewInterface;
use tiFy\Kernel\Templates\TemplateController;

class FormView extends TemplateController implements FormViewInterface
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [];

    /**
     * {@inheritdoc}
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->mixins)) :
            return call_user_func_array(
                [$this->engine->get('form'), $name],
                $arguments
            );
        endif;
    }
}