<?php

namespace tiFy\Partial;

use tiFy\View\ViewController;

/**
 * Class PartialView
 *
 * @method string after()
 * @method string attrs()
 * @method string before()
 * @method string content()
 * @method string getId()
 * @method string getIndex()
 */
class PartialView extends ViewController
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [
        'after',
        'attrs',
        'before',
        'content',
        'getId',
        'getIndex'
    ];

    /**
     * Translation d'appel des méthodes de l'application associée.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->mixins)) :
            return call_user_func_array(
                [$this->engine->get('partial'), $name],
                $arguments
            );
        elseif (method_exists($this, $name)) :
            return call_user_func_array([$this, $name], $arguments);
        endif;

        return null;
    }
}