<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use Illuminate\Database\Eloquent\Builder;
use tiFy\Contracts\Support\ParamsBag;

interface FactoryQueryBuilder extends FactoryAwareTrait, ParamsBag
{
    /**
     * Récupération de la liste des colonnes de la table.
     *
     * @return string[]
     */
    public function getColumns(): array;

    /**
     * Vérification d'existance d'une colonne de la table selon son nom de qualification.
     *
     * @param string $name Nom de qalification de la colonne de la table de base de données.
     *
     * @return boolean
     */
    public function hasColumn(string $name): bool;

    /**
     * Récupération de l'instance du gestionnaire de base de données.
     *
     * @return FactoryDb|null
     */
    public function db(): ?FactoryDb;

    /**
     * Aggrégation des conditions de limitation de la requête de récupération des éléments.
     *
     * @return Builder
     */
    public function limitClause(): Builder;

    /**
     * Aggrégation des conditions d'ordonnancement de la requête de récupération des éléments.
     *
     * @return Builder
     */
    public function orderClause(): Builder;

    /**
     * Récupération du numéro de la page courante.
     *
     * @return int
     */
    public function pageNum(): int;

    /**
     * Récupération du nombre d'éléments par page.
     *
     * @return int
     */
    public function perPage(): int;

    /**
     * Définition du nombre d'éléments par page.
     *
     * @param int $num
     *
     * @return static
     */
    public function setPerPage(int $num): FactoryQueryBuilder;

    /**
     * Traitement de la requête de récupération des éléments.
     *
     * @return iterable
     */
    public function proceed(): iterable;

    /**
     * Récupération de l'instance courante en base de données.
     *
     * @return Builder|null
     */
    public function query(): ?Builder;

    /**
     * Aggrégation des conditions de filtrage de la requête de récupération des éléments.
     *
     * @return Builder
     */
    public function whereClause(): Builder;
}