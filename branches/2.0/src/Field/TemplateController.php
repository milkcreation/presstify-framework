<?php

namespace tiFy\Field;

use Illuminate\Support\Arr;
use League\Plates\Engine;
use tiFy\Apps\Templates\AppTemplateController;

/**
 * Class TemplateController
 *
 * @method array all() Récupération de la liste complète des attributs de configuration.
 * @method string attrs() Linéarisation de la liste des attributs HTML du champ.
 * @method mixed get(string $key, mixed $default = '') Récupération d'un attribut de configuration.
 * @method string getName() Récupération de l'attribut de configuration de la qualification de soumission du champ "name".
 * @method mixed getValue() Récupération de l'attribut de configuration de la valeur initiale de soumission du champ "value".
 * @method bool has(string $key) Vérification d'existance d'un attribut de configuration.
 * @method string htmlAttrs(array $attrs) Linéarisation d'une liste d'attributs HTML.
 */
class TemplateController extends AppTemplateController
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