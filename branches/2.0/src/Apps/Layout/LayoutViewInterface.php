<?php

namespace tiFy\Apps\Layout;

use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\Layout\LayoutControllerInterface;

interface LayoutViewInterface extends AppControllerInterface
{
    /**
     * Récupération de l'environnement d'affichage de la vue.
     *
     * @return string admin|user
     */
    public function getEnv();

    /**
     * Récupération du contexte d'affichage.
     *
     * @return bool
     */
    public function isAdmin();

    /**
     * Récupération du controleur d'affichage.
     *
     * @return LayoutControllerInterface
     */
    public function layout();
}