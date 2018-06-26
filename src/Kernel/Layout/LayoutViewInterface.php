<?php

namespace tiFy\Kernel\Layout;

use tiFy\Apps\AppControllerInterface;
use tiFy\Kernel\Layout\LayoutControllerInterface;

interface LayoutViewInterface extends AppControllerInterface
{
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