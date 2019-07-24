<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Contracts\Support\ParamsBag;

interface FactoryBuilder extends FactoryAwareTrait, ParamsBag
{
    /**
     * Récupération de l'instance du gestionnaire de base de données.
     *
     * @return FactoryDb|null
     */
    public function db(): ?FactoryDb;

    /**
     * Récupération du sens d'ordonnancement des éléments récupérés.
     *
     * @return string
     */
    public function getOrder(): string;

    /**
     * Récupération de la colonne d'ordonnancement des éléments.
     *
     * @return string
     */
    public function getOrderBy(): string;

    /**
     * Récupération du numéro de la page courante.
     *
     * @return int
     */
    public function getPage(): int;

    /**
     * Récupération du nombre d'éléments par page.
     *
     * @return int
     */
    public function getPerPage(): int;

    /**
     * Récupération de l'instance courante en base de données.
     *
     * @return EloquentBuilder|null
     */
    public function query(): ?EloquentBuilder;

    /**
     * Aggrégation des conditions de limitation de la requête de récupération des éléments.
     *
     * @return EloquentBuilder
     */
    public function queryLimit(): EloquentBuilder;

    /**
     * Aggrégation des conditions d'ordonnancement de la requête de récupération des éléments.
     *
     * @return EloquentBuilder
     */
    public function queryOrder(): EloquentBuilder;

    /**
     * Aggrégation des conditions de filtrage de la requête de récupération des éléments.
     *
     * @return EloquentBuilder
     */
    public function queryWhere(): EloquentBuilder;

    /**
     * Suppression d'un ou plusieurs attributs de requête de récupération des éléments.
     *
     * @param string|array $keys Clé d'indice des attributs. Syntaxe à point permise.
     *
     * @return static
     */
    public function remove($keys): FactoryBuilder;

    /**
     * Réinitialisation de la requête de récupération des éléments.
     *
     * @return static
     */
    public function resetQuery(): FactoryBuilder;

    /**
     * Définition du sens d'ordonnancement de récupération de éléments.
     *
     * @param string $order
     *
     * @return static
     */
    public function setOrder(string $order): FactoryBuilder;

    /**
     * Définition de la colonne d'ordonnancement des éléments.
     *
     * @param string $order_by
     *
     * @return static
     */
    public function setOrderBy(string $order_by): FactoryBuilder;

    /**
     * Définition du numéro de la page courante.
     *
     * @param int $page
     *
     * @return static
     */
    public function setPage(int $page): FactoryBuilder;

    /**
     * Définition du nombre d'éléments par page.
     *
     * @param int $per_page
     *
     * @return static
     */
    public function setPerPage(int $per_page): FactoryBuilder;
}