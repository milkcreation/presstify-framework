<?php

namespace tiFy\Apps\Layout;

use tiFy\Apps\AppInterface;
use tiFy\Apps\Layout\LayoutInterface;

interface LayoutViewInterface extends AppInterface
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
     * @return LayoutInterface
     */
    public function layout();
}