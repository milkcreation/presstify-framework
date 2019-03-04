<?php

namespace tiFy\Contracts\User;

use tiFy\Contracts\Kernel\ParamsBag;

interface SignUpController extends ParamsBag
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getName();

    /**
     * Affichage du formulaire.
     *
     * @return string
     */
    public function form();
}