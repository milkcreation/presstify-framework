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
     * @param array $args Liste des arguments de requête.
     *
     * @return void
     */
    public function query($args);

    /**
     * Définition du nombre total d'éléments trouvés.
     *
     * @return $this
     */
    public function setFounds($founds);
}