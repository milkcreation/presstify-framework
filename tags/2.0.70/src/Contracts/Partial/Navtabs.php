<?php

namespace tiFy\Contracts\Partial;

interface Navtabs extends PartialController
{
    /**
     * Mise à jour de l'onglet courant via Ajax.
     *
     * @return void
     */
    public function wp_ajax();
}