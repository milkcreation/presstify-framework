<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Template\FactoryRequest;

interface Request extends FactoryRequest
{
    /**
     * Récupération du nombre d'éléments par page.
     *
     * @return int
     */
    public function getPerPage(): int;

    /**
     * Récupération du numéro de la page courante.
     *
     * @return int
     */
    public function getPagenum(): int;

    /**
     * Récupération du la liste des arguments de requête.
     *
     * @return array
     */
    public function getQueryArgs(): array;

    /**
     * Vérifie si la requête HTTP courante répond à une recherche.
     *
     * @return boolean
     */
    public function searchExists(): bool;

    /**
     * Récupération du terme de recherche.
     *
     * @return string
     */
    public function searchTerm(): string;
}