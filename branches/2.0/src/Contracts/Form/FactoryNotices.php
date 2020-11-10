<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Kernel\Notices;
use tiFy\Support\ParamsBag;

interface FactoryNotices extends FactoryResolver, Notices
{
    /**
     * Récupération d'un paramètre ou de l'intance du contrôleur des paramètres.
     *
     * @param null|string $key Clé d'indexe du paramètres à récupérer. Syntaxe à points permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed|ParamsBag
     */
    public function params($key = null, $default = null);
}