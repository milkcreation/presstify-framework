<?php

namespace tiFy\Form\Forms;

use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Forms\FormItemController;

class FormCallbacksController extends AbstractCommonDependency
{
    /**
     * Liste des fonctions de rappel déclarées.
     * @var array
     */
    protected $registered = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param FormItemController $Form Classe de rappel du controleur de formulaire associé.
     * @param array $callbacks Liste des fonctions de court-circuitage.
     *
     * @return void
     */
    public function __construct(FormItemController $form, $callbacks = [])
    {
        parent::__construct($form);

        foreach ($callbacks as $hookname => $attrs) :
            if (is_callable($attrs)) :
                $callable = $attrs;
                $priority = 10;
            elseif (isset($attrs['cb'])) :
                $callable = $attrs['cb'];
                $priority = isset($attrs['priority']) ? $attrs['priority'] : 10;
            else:
                continue;
            endif;

            $this->set($hookname, $callable, $priority);
        endforeach;
    }

    /**
     * Définition d'une fonction de court-circuitage.
     *
     * @param string $hookname Nom de qualification du court-circuitage.
     * @param array $callable Méthode ou fonction de rappel.
     * @param int Ordre de priorité d'éxecution.
     *
     * @return void
     */
    public function set($hookname, $callable, $priority = 10)
    {
        $priority = absint($priority);

        if (!isset($this->registered[$hookname])) :
            $this->registered[$hookname] = [];
        endif;

        if (!isset($this->registered[$hookname][$priority])) :
            $this->registered[$hookname][$priority] = [];
        endif;

        array_push($this->registered[$hookname][$priority], $callable);
    }

    /**
     * Appel d'une fonction de court-circuitage.
     *
     * @param string $hookname Nom de qualification du court-circuitage
     * @param array $args Liste des arguments passés dans l'appel de la méthode
     *
     * @return null|callable
     */
    public function call($hookname, $args = [])
    {
        if ($controller = $this->getController()) :
            $this->getController()->call($hookname, $args);
        endif;

        if (!isset($this->registered[$hookname])) :
            return null;
        endif;

        ksort($this->registered[$hookname]);

        foreach ($this->registered[$hookname] as $priority => $functions) :
            foreach ($functions as $callable) :
                if (!is_callable($callable)) :
                    continue;
                endif;

                call_user_func_array($callable, $args);
            endforeach;
        endforeach;
    }
}