<?php

namespace tiFy\App\Partial;

use App\Kernel\Item\AbstractAppItemController;
use tiFy\App\AppControllerInterface;

abstract class AbstractAppPartial extends AbstractAppItemController
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
     * @var AppControllerInterface
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