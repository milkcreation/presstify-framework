<?php

namespace tiFy\Metabox;

use tiFy\Kernel\Templates\TemplateController;

/**
 * Class PartialViewTemplate
 *
 * @method string after().
 * @method string attrs().
 * @method string before().
 * @method string content().
 * @method string getHtmlAttrs().
 * @method string getId().
 * @method string getIndex().
 */
class MetaboxView extends TemplateController
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [

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
                [$this->engine->get('metabox'), $name],
                $arguments
            );
        endif;
    }
}