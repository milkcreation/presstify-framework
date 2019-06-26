<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Contracts\Template\{FactoryDb, FactoryBuilder as FactoryBuilderContract, TemplateFactory};
use tiFy\Support\ParamsBag;

class FactoryBuilder extends ParamsBag implements FactoryBuilderContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $factory;

    /**
     * Liste des colonnes de la table de base de données.
     * @var string[]|null
     */
    protected $columns;

    /**
     * Colonne d'ordonnancement des éléments.
     * @var string
     */
    protected $orderby = '';

    /**
     * Sens d'ordonnancement des éléments.
     * @var string
     */
    protected $order = '';

    /**
     * Numéro de la page d'affichage courante.
     * @var int
     */
    protected $pageNum = 0;

    /**
     * Nombre d'éléments affichés par page.
     * @var int|null
     */
    protected $perPage = 0;

    /**
     * Instance de la requête courante en base de données.
     * @var EloquentBuilder|null
     */
    protected $query;

    /**
     * @inheritDoc
     */
    public function db(): ?FactoryDb
    {
        return $this->factory->db();
    }

    /**
     * @inheritDoc
     */
    public function getColumns(): array
    {
        if (is_null($this->columns)) {
            $this->columns = $this->db()->getConnection()->getSchemaBuilder()->getColumnListing($this->db()->getTable())
                ?: [];
        }

        return $this->columns;
    }

    /**
     * @inheritDoc
     */
    public function hasColumn(string $name): bool
    {
        return in_array($name, $this->getColumns());
    }

    /**
     * @inheritDoc
     */
    public function pageNum(): int
    {
        return (int)$this->pageNum;
    }

    /**
     * @inheritDoc
     */
    public function parse(): FactoryBuilderContract
    {
        parent::parse();

        $this->set($this->factory->request()->all());

        $this->perPage = $this->pull('per_page', 20);
        $this->pageNum = $this->pull('paged', 1) ?: 1;

        $order = strtolower($this->pull('order', $this->factory->param('order', 'desc')));
        $this->order = in_array($order, ['asc', 'desc']) ? $order : 'desc';

        $this->orderby = $this->pull('orderby', '');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function perPage(): int
    {
        return (int)$this->perPage;
    }

    /**
     * @inheritDoc
     */
    public function query(): ?EloquentBuilder
    {
        if (is_null($this->query)) {
            $this->query = $this->db() ? $this->db()::query() : null;
        }

        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function queryLimit(): EloquentBuilder
    {
        return $this->query()->forPage($this->pageNum, $this->perPage);
    }

    /**
     * @inheritDoc
     */
    public function queryOrder(): EloquentBuilder
    {
        if (!$this->orderby) {
            $this->orderby = $this->db()->getKeyName();
        }

        return $this->query()->orderBy($this->orderby, $this->order);
    }

    /**
     * @inheritDoc
     */
    public function queryWhere(): EloquentBuilder
    {
        foreach ($this->all() as $k => $v) {
            if ($this->hasColumn($k)) {
                is_array($v) ? $this->query()->whereIn($k, $v) : $this->query()->where($k, $v);
            }
        }

        return $this->query();
    }

    /**
     * @inheritDoc
     */
    public function remove($keys): FactoryBuilderContract
    {
        $this->forget($keys);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resetQuery(): FactoryBuilderContract
    {
        $this->query = null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPerPage(int $per_page): FactoryBuilderContract
    {
        $this->perPage = $per_page > 0 ? $per_page : 0;

        return $this;
    }
}