<?php declare(strict_types=1);

namespace tiFy\Database;

use Illuminate\Database\Eloquent\{
    Collection as DbCollection,
    Builder as DbBuilder
};
use tiFy\Contracts\Database\{Model as ModelContract, ModelQuery as ModelQueryContract};
use tiFy\Support\{ParamsBag, Str};

class ModelQuery extends ParamsBag implements ModelQueryContract
{
    /**
     * Liste des classes de rappel d'instanciation selon un critère.
     * @var string[][]|array
     */
    protected static $builtInClasses = [];

    /**
     * Classe du modèle associé.
     * @var string
     */
    protected static $builtInModelClass = '';

    /**
     * Classe de rappel d'instanciation.
     * @var string
     */
    protected static $fallbackClass = '';

    /**
     * Instance du modèle Eloquent associé.
     * @var ModelContract|null
     */
    protected $model;

    /**
     * CONSTRUCTEUR
     *
     * @param ModelContract|null $model
     *
     * @return void
     */
    public function __construct(?ModelContract $model = null)
    {
        if ($this->model = $model instanceof ModelContract ? $model : null) {
            $this->set($model->getAttributes())->parse();
        }
    }

    /**
     * @inheritDoc
     */
    public static function build(object $model): ?ModelQueryContract
    {
        if (!$model instanceof ModelContract) {
            return null;
        }

        $class = self::$fallbackClass ?: static::class;

        return class_exists($class) ? new $class($model) : new static($model);
    }

    /**
     * @inheritDoc
     */
    public static function builtInModel(): ?ModelContract
    {
        if (!$modelClass = static::$builtInModelClass) {
            return null;
        } elseif(!($model = new $modelClass()) instanceof ModelContract) {
            return null;
        }

        return $model;
    }

    /**
     * @inheritDoc
     */
    public static function create($id = null, ...$args): ?ModelQueryContract
    {
        if (is_numeric($id)) {
            return static::createFromId((int)$id);
        } elseif ($id instanceof ModelContract) {
            return static::build($id);
        } elseif ($id instanceof ModelQueryContract) {
            return static::createFromId($id->getId());
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function createFromId(int $id): ?ModelQueryContract
    {
        return ($model = static::builtInModel()) && ($instance = $model->find($id))  ? static::build($instance) : null;
    }

    /**
     * @inheritDoc
     */
    public static function fetch($query = null): array
    {
        if (is_array($query)) {
            return static::fetchFromArgs($query);
        } elseif ($query instanceof DbCollection) {
            return static::fetchFromEloquent($query);
        } else {
            return [];
        }
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromArgs(array $args = []): array
    {
        return ($query = static::parseQueryArgs($args)) ? static::fetchFromEloquent($query->get()) : [];
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromEloquent(DbCollection $collection): array
    {
        $instances = [];
        foreach ($collection as $model) {
            $instances[] = static::build($model);
        }

        return $instances;
    }

    /**
     * @inheritDoc
     */
    public static function keyName(): ?string
    {
        return ($model = static::builtInModel()) && ($key = $model->getKeyName()) ? $key : null;
    }

    /**
     * @inheritDoc
     */
    public static function parseQueryArgs(array $args = []): ?DbBuilder
    {
        if (!$model = static::builtInModel()) {
            return null;
        }

        $columns = $model->getColumns();
        $query = $model->newQuery();

        foreach ($columns as $c) {
            if (isset($args[$c])) {
                $v = $args[$c];

                $method = 'parseQueryArg' . Str::studly($c);

                if (method_exists(__CLASS__, $method)) {
                    $query = static::{$method}($v, $query);
                } else {
                    if (is_array($v)) {
                        if (isset($v['value']) && isset($v['compare'])) {
                            $query->where($c, $v['compare'], $v['value']);
                        } else {
                            $query->whereIn($c, $v);
                        }
                    } else {
                        $query->where($c, $v);
                    }
                }
            }
        }

        $query = static::parseQueryArgOrderBy($order = $args['order_by'] ?? null, $query);

        $query = isset($args['per_page']) && is_numeric($args['per_page'])
            ? static::parseQueryArgPerPage((int)$args['per_page'], $query) : $query;


        return $query;
    }

    /**
     * @inheritDoc
     */
    public static function parseQueryArgOrderBy($order, DbBuilder $query): DbBuilder
    {
        if (is_string($order)) {
            $query->orderBy($order);
        } elseif (is_array($order)) {
            foreach ($order as $col => $dir) {
                if (is_numeric($col)) {
                    $col = $dir;
                    $dir = 'asc';
                }
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }

    /**
     * @inheritDoc
     */
    public static function parseQueryArgPerPage(int $limit, DbBuilder $query): DbBuilder
    {
        if (($limit > 0)) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * @inheritDoc
     */
    public static function setBuiltInModelClass(string $classname): void
    {
        static::$builtInModelClass = $classname;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->get(static::keyName());
    }
}