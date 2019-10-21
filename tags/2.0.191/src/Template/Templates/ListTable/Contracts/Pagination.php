<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Template\FactoryAwareTrait;

interface Pagination extends FactoryAwareTrait, ParamsBag
{
    /**
     * Récupération du nombre d'éléments de la page courante.
     *
     * @return int
     */
    public function getCount(): int;

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
     * Récupération de l'emplacement d'affichage de l'interface de pagination.
     *
     * @return string
     */
    public function getWhich(): string;

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
     * Récupération de l'url vers une page.
     *
     * @param int $page Numéro de la page.
     *
     * @return string
     */
    public function pagedUrl(int $page): string;

    /**
     * {@inheritDoc}
     *
     * @return Pagination
     */
    public function parse(): Pagination;

    /**
     * Traitement des attributs de configuration de la première page.
     *
     * @return Pagination
     */
    public function parseFirst(): Pagination;

    /**
     * Traitement des attributs de configuration de la dernière page.
     *
     * @return Pagination
     */
    public function parseLast(): Pagination;

    /**
     * Traitement des attributs de configuration de la page suivante.
     *
     * @return Pagination
     */
    public function parseNext(): Pagination;

    /**
     * Traitement des attributs de configuration de la page précédente.
     *
     * @return Pagination
     */
    public function parsePrev(): Pagination;

    /**
     * Définition du nombre d'élément sur la page courante.
     *
     * @param int $count
     *
     * @return static
     */
    public function setCount(int $count): Pagination;

    /**
     * Définition du numéro de la page courante.
     *
     * @param int $num
     *
     * @return static
     */
    public function setCurrentPage(int $num): Pagination;

    /**
     * Définition du numéro de la dernière page.
     *
     * @param int $num
     *
     * @return static
     */
    public function setLastPage(int $num): Pagination;

    /**
     * Définition du nombre d'éléments par page.
     *
     * @param int $per_page
     *
     * @return static
     */
    public function setPerPage(int $per_page): Pagination;

    /**
     * Définition du nombre total d'éléments.
     *
     * @param int $total
     *
     * @return static
     */
    public function setTotal(int $total): Pagination;

    /**
     * Définition de l'emplacement d'affichage.
     *
     * @param string $which top|bottom
     *
     * @return static
     */
    public function setWhich(string $which): Pagination;

    /**
     * Récupération de l'url de la page courante sans l'argument de pagination.
     *
     * @return string
     */
    public function unpagedUrl(): string;
}