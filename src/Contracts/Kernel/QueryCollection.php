<?php

namespace tiFy\Contracts\Kernel;

interface QueryCollection extends Collection
{
    /**
     * Récupération du nombre total d'éléments trouvés.
     *
     * @return int
     */
    public function getFounds();

    /**
     * Traitement de la requête de récupération des éléments.
     *
     * @return void
     */
    public function query();

    /**
     * Définition du nombre total d'éléments trouvés.
     *
     * @return $this
     */
    public function setFounds($founds);
}