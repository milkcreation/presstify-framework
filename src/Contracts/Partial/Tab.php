<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

interface Tab extends PartialFactory
{
    /**
     * Mise à jour de l'onglet courant via Ajax.
     *
     * @return void
     */
    public function wp_ajax();
}