<?php

namespace tiFy\Kernel\Layout\Request;

interface RequestInterface
{
    /**
     * Récupération de l'url courante.
     *
     * @return string
     */
    public function currentUrl();
}