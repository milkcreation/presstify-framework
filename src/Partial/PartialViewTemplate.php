<?php

namespace tiFy\Partial;

use Illuminate\Support\Arr;
use League\Plates\Engine;
use tiFy\Contracts\Partial\PartialItemInterface;
use tiFy\Kernel\Templates\TemplateController;

/**
 * Class PartialViewTemplate
 *
 * @method string after().
 * @method string attrs().
 * @method string before().
 * @method string content().
 * @method string getId().
 * @method string getIndex().
 */
class PartialViewTemplate extends TemplateController
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $inherits = [
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
        if (in_array($name, $this->inherits)) :
            return call_user_func_array(
                [$this->engine->get('partial'), $name]
                , $arguments
            );
        endif;
    }
}