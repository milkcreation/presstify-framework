<?php

namespace tiFy\Field;

use tiFy\View\ViewController;

/**
 * Class FieldView
 *
 * @method string after().
 * @method string attrs().
 * @method string before().
 * @method string content().
 * @method string getId().
 * @method string getIndex().
 * @method string getName().
 * @method string getValue().
 * @method string options().
 */
class FieldView extends ViewController
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
        'getIndex',
        'getName',
        'getValue',
        'options'
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
                [$this->engine->get('field'), $name],
                $arguments
            );
        else :
            return null;
        endif;
    }
}