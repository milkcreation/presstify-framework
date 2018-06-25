<?php

namespace tiFy\AdminView\Request;

interface RequestInterface
{
    /**
     * Récupération de l'url courante.
     *
     * @return string
     */
    public function currentUrl();
}