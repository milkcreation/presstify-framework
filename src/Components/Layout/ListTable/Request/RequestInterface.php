<?php

namespace tiFy\Components\Layout\ListTable\Request;

use tiFy\Kernel\Layout\Request\RequestInterface as KernelRequestInterface;

interface RequestInterface extends KernelRequestInterface
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