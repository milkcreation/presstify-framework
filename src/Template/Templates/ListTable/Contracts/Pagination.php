<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Template\FactoryAwareTrait;

interface Pagination extends FactoryAwareTrait, ParamsBag
{
    /**
     * Rendu d'affichage de la page courante.
     *
     * @return string
     */
    public function currentPage(): string;

    /**
     * Rendu d'affichage de l'accès à la première page.
     *
     * @return string
     */
    public function firstPage(): string;

    /**
     * Récupération du numéro de la page courante.
     *
     * @return int
     */
    public function getCurrentPage(): int;

    /**
     * Récupération du nombre total de page d'éléments.
     *
     * @return int
     */
    public function getLastPage(): int;

    /**
     * Récupération du nombre d'éléments affiché par page.
     *
     * @return int
     */
    public function getPerPage(): int;

    /**
     * Récupération du nombre total d'éléments.
     *
     * @return int
     */
    public function getTotal(): int;

    /**
     * Vérification de la désactivation du lien vers la première page.
     *
     * @return boolean
     */
    public function isDisableFirst(): bool;

    /**
     * Vérification de la désactivation du lien vers la dernière page.
     *
     * @return boolean
     */
    public function isDisableLast(): bool;

    /**
     * Vérification de la désactivation du lien vers la page suivante.
     *
     * @return boolean
     */
    public function isDisableNext(): bool;

    /**
     * Vérification de la désactivation du lien vers la page précédente.
     *
     * @return boolean
     */
    public function isDisablePrev(): bool;

    /**
     * Vérification de l'activation de la pagination par infinite scroll.
     *
     * @return boolean
     */
    public function isInfiniteScroll(): bool;

    /**
     * Rendu d'affichage de l'accès à la dernière page.
     *
     * @return string
     */
    public function lastPage(): string;

    /**
     * Rendu d'affichage de l'accès à la page suivante.
     *
     * @return string
     */
    public function nextPage(): string;

    /**
     * Récupération de l'url vers une page.
     *
     * @param int $page Numéro de la page.
     *
     * @return string
     */
    public function pagedUrl(int $page): string;

    /**
     * {@inheritdoc}
     *
     * @return Pagination
     */
    public function parse(): Pagination;

    /**
     * Rendu d'affichage de l'accès à la page précédente.
     *
     * @return string
     */
    public function prevPage(): string;

    /**
     * Récupération de l'url de la page courante sans l'argument de pagination.
     *
     * @return string
     */
    public function unpagedUrl(): string;

    /**
     * Définition de l'emplacement d'affichage.
     *
     * @param string $which top|bottom
     *
     * @return static
     */
    public function which(string $which): Pagination;
}