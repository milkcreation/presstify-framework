<?php

namespace tiFy\Contracts\PostType;

use tiFy\Contracts\Kernel\QueryCollection;

interface PostQueryCollection extends QueryCollection
{
    /**
     * Récupération de la liste des identifiants de qualification.
     *
     * @return array
     */
    public function getIds();

    /**
     * Récupération de la liste des intitulés de qualification.
     *
     * @return array
     */
    public function getTitles();
}