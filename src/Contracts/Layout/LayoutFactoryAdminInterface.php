<?php

namespace tiFy\Contracts\Layout;

interface LayoutFactoryAdminInterface extends LayoutFactoryInterface
{
    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getScreen();
}