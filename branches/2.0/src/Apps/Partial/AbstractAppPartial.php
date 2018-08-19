<?php

namespace tiFy\Apps\Partial;

use tiFy\Apps\AppInterface;
use tiFy\Apps\Item\AbstractAppItemController;

abstract class AbstractAppPartial extends AbstractAppItemController
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * Affichage.
     *
     * @return string
     */
    abstract function display();

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->display();
    }
}