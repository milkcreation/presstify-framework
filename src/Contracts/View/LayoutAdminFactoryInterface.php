<?php

namespace tiFy\Contracts\Layout;

interface LayoutAdminFactoryInterface extends LayoutFactoryInterface
{
    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getScreen();
}