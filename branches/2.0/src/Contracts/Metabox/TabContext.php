<?php declare(strict_types=1);

namespace tiFy\Contracts\Metabox;

interface TabContext extends MetaboxContext
{
    /**
     * Définition de l'onglet actif.
     *
     * @param string $tab
     *
     * @return static
     */
    public function setActive(string $tab): TabContext;
}
