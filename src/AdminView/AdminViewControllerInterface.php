<?php

namespace tiFy\AdminView;

use tiFy\Kernel\Layout\LayoutViewInterface;

interface AdminViewControllerInterface extends LayoutViewInterface
{
    /**
     * Récupération du nom de qualification d'accroche de la page d'affichage de l'interface.
     *
     * @return string
     */
    public function getHookname();

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getScreen();
}