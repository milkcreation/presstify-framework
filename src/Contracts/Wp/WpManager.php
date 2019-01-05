<?php

namespace tiFy\Contracts\Wp;

interface WpManager
{
    /**
     * Indicateur d'environnement Worpress.
     *
     * @return boolean
     */
    public function is();
}