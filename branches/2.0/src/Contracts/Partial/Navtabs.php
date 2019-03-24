<?php

namespace tiFy\Contracts\Partial;

interface Navtabs extends PartialFactory
{
    /**
     * Mise à jour de l'onglet courant via Ajax.
     *
     * @return void
     */
    public function wp_ajax();
}