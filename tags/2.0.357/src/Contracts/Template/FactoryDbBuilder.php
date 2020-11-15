<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

interface FactoryDbBuilder extends FactoryAwareTrait, FactoryBuilder
{
    /**
     * Récupération de l'instance du gestionnaire de base de données.
     *
     * @return FactoryDb|null
     */
    public function db(): ?FactoryDb;

    /**
     * Récupération de la liste des colonnes de la table de base de données.
     *
     * @return array
     */
    public function getColumns(): array;

    /**
     * Récupération du nom de la colonne de clé primaire.
     *
     * @return string
     */
    public function getKeyName(): string;

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
     * Aggrégation des conditions de recherche de la requête de récupération des éléments.
     *
     * @return EloquentBuilder
     */
    public function querySearch(): EloquentBuilder;

    /**
     * Aggrégation des conditions de filtrage de la requête de récupération des éléments.
     *
     * @return EloquentBuilder
     */
    public function queryWhere(): EloquentBuilder;

    /**
     * Réinitialisation de la requête de récupération des éléments.
     *
     * @return static
     */
    public function resetQuery(): FactoryDbBuilder;

    /**
     * Définition de la liste des colonnes de la table de base de données.
     *
     * @param array $columns
     *
     * @return static
     */
    public function setColumns(array $columns): FactoryDbBuilder;

    /**
     * Définition du nom de la colonne de clé primaire de la table de base de données.
     *
     * @param string $keyName
     *
     * @return static
     */
    public function setKeyName(string $keyName): FactoryDbBuilder;
}