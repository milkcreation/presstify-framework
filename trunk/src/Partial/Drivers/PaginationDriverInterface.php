<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\Drivers\Pagination\PaginationQueryInterface;
use tiFy\Partial\PartialDriverInterface;

interface PaginationDriverInterface extends PartialDriverInterface
{
    /**
     * Récupération d'un séparateur de nombre.
     *
     * @param array $numbers Liste des numéros de page existants.
     *
     * @return void
     */
    public function ellipsis(array &$numbers): void;

    /**
     * Boucle de récupération des numéros de page.
     *
     * @param array $numbers Liste des numéros de page existants.
     * @param int $start Démarrage de la boucle de récupération.
     * @param int $end Fin de la boucle de récupération.
     *
     * @return void
     */
    public function numLoop(array &$numbers, int $start, int $end): void;

    /**
     * Traitement de la liste des liens.
     *
     * @return static
     */
    public function parseLinks(): PaginationDriverInterface;

    /**
     * Traitement de la liste des numéros de page.
     *
     * @return static
     */
    public function parseNumbers(): PaginationDriverInterface;

    /**
     * Traitement de l'instance du gestionnaire de requête de récupération des éléments.
     *
     * @return static
     */
    public function parseQuery(): PaginationDriverInterface;

    /**
     * Traitement des arguments d'url.
     *
     * @return static
     */
    public function parseUrl(): PaginationDriverInterface;

    /**
     * Récupération de l'instance du gestionnaire de requête de récupération des arguments de pagination.
     *
     * @return PaginationQueryInterface|null
     */
    public function query(): ?PaginationQueryInterface;
}