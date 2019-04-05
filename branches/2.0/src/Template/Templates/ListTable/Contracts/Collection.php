<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\Collection as tiFyCollection;

interface Collection extends tiFyCollection
{
    /**
     * Traitement de récupération de la liste des éléments.
     *
     * @param array $query_args Liste des arguments de requête de récupération
     *
     * @return static
     */
    public function query(array $query_args = []): Collection;

    /**
     * Récupération du nombre total d'éléments trouvés.
     *
     * @return int
     */
    public function total(): int;
}