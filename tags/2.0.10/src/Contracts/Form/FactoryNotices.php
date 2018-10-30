<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Kernel\ParamsBagInterface;
use tiFy\Contracts\Kernel\Notices;
use tiFy\Contracts\Form\FactoryResolver;

interface FactoryNotices extends FactoryResolver, Notices
{
    /**
     * Récupération d'un paramètre ou de l'intance du contrôleur des paramètres.
     *
     * @param null|string $key Clé d'indexe du paramètres à récupérer. Syntaxe à points permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed|ParamsBagInterface
     */
    public function params($key = null, $default = null);
}