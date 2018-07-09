<?php

namespace tiFy\Field;

use Illuminate\Support\Arr;
use League\Plates\Engine;
use tiFy\Apps\Templates\TemplateBaseController;

/**
 * Class TemplateController
 *
 * @method array all() all() Récupération de la liste complète des attributs de configuration.
 * @method mixed get() get(string $key, mixed $default = '') Récupération d'un attribut de configuration.
 * @method bool has() has(string $key) Vérification d'existance d'un attribut de configuration.
 * @method string htmlAttrs() htmlAttrs(array $attrs) Linéarisation d'une liste d'attributs HTML.
 *
 */
class TemplateController extends TemplateBaseController
{
    /**
     * Classe de rappel de l'application associée.
     * @var FieldItemInterface
     */
    protected $app;

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
        if (method_exists($this->app, $name)) :
            return call_user_func_array([$this->app, $name], $arguments);
        endif;
    }
}