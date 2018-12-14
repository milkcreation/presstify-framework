<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface RowActionsItem extends ParamsBag
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Affichage.
     *
     * @return string
     */
    public function display();

    /**
     * Récupération de l'identifiant de qualification de la clef de sécurisation d'une action sur un élément.
     *
     * @return string
     */
    public function getNonce();

    /**
     * Vérification d'activation de l'action.
     *
     * @return boolean
     */
    public function isActive();
}