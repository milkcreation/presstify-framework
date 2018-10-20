<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Views\ViewInterface;

interface FormView extends ViewInterface
{
    /**
     * Translation d'appel des méthodes de l'application associée.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     */
    public function __call($name, $arguments);
}