<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Contracts\Layout\LayoutDisplayRequestInterface;

interface RequestInterface extends LayoutDisplayRequestInterface
{
    /**
     * Récupération du nombre d'éléments affichés par page.
     *
     * @return int
     */
    public function getPerPage();

    /**
     * Récupération du numero de la page d'affichage courant.
     *
     * @return int
     */
    public function getPagenum();
}