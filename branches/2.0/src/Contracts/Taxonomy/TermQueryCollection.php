<?php

namespace tiFy\Contracts\Taxonomy;

interface TermQueryCollection
{
    /**
     * Récupération de la liste des identifiants de qualification.
     *
     * @return array
     */
    public function getIds();

    /**
     * Récupération de la liste des identifiants de qualification.
     *
     * @return array
     */
    public function getNames();

    /**
     * Récupération de la liste des identifiants de qualification.
     *
     * @return array
     */
    public function getSlugs();
}