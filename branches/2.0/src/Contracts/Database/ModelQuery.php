<?php declare(strict_types=1);

namespace tiFy\Contracts\Database;

use Illuminate\Database\Eloquent\{Collection as DbCollection, Builder as DbBuilder};
use tiFy\Contracts\Database\Model as ModelContract;
use tiFy\Contracts\Support\ParamsBag;

interface ModelQuery extends ParamsBag
{
    /**
     * Création d'une instance basée sur un modèle et selon la cartographie des classes de rappel.
     *
     * @param ModelContract|object $model
     *
     * @return static
     */
    public static function build(object $model): ?ModelQuery;

    /**
     * Instance de la classe du modèle associé.
     *
     * @return ModelContract|null
     */
    public static function builtInModel(): ?ModelContract;

    /**
     * Création d'une instance basée sur un argument de qualification.
     *
     * @param int|string|Model|ModelQuery|null $id
     * @param array ...$args Liste des arguments de qualification complémentaires.
     *
     * @return static|null
     */
    public static function create($id = null, ...$args): ?ModelQuery;

    /**
     * Récupération d'une instance basée sur l'indice de clé primaire.
     *
     * @param int $id Indice de clé primaire.
     *
     * @return static|null
     */
    public static function createFromId(int $id): ?ModelQuery;

    /**
     * Récupération d'une liste d'instances selon une requête en base|selon une liste d'arguments.
     *
     * @param DbCollection|array|null $query
     *
     * @return ModelQuery[]|array
     */
    public static function fetch($query = null): array;

    /**
     * Récupération d'une liste d'instance depuis une liste d'arguments.
     *
     * @param array $args
     *
     * @return static[]|array
     */
    public static function fetchFromArgs(array $args = []): array;

    /**
     * Récupération d'une liste d'instance basée sur un résultat de requête Eloquent.
     *
     * @param DbCollection $collection
     *
     * @return static[]|array
     */
    public static function fetchFromEloquent(DbCollection $collection): array;

    /**
     * Récupération du nom de qualification de la clé primaire.
     *
     * @return string|null
     */
    public static function keyName(): ?string;

    /**
     * Traitement d'une requête de récupération d'éléments selon une liste d'arguments.
     *
     * @param array $args
     *
     * @return DbBuilder|null
     */
    public static function parseQueryArgs(array $args = []): ?DbBuilder;

    /**
     * Traitement de l'ordonnancement d'une requête de récupération d'éléments.
     *
     * @param string|array|null $order
     * @param DbBuilder $query
     *
     * @return DbBuilder
     */
    public static function parseQueryArgOrderBy($order, DbBuilder $query): DbBuilder;

    /**
     * Traitement de la limitation d'une requête de récupération d'éléments.
     *
     * @param int $limit
     * @param DbBuilder $query
     *
     * @return DbBuilder
     */
    public static function parseQueryArgPerPage(int $limit, DbBuilder $query): DbBuilder;

    /**
     * Définition de la classe du modèle associé.
     *
     * @param string $classname
     *
     * @return void
     */
    public static function setBuiltInModelClass(string $classname): void;

    /**
     * Récupération de l'indice de la clé primaire.
     *
     * @return string|int|null
     */
    public function getId();
}