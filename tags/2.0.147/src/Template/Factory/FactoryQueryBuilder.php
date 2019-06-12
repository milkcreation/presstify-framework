<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use Illuminate\Database\Eloquent\Builder;
use tiFy\Contracts\Template\{FactoryDb, FactoryQueryBuilder as FactoryQueryBuilderContract, TemplateFactory};
use tiFy\Support\ParamsBag;

class FactoryQueryBuilder extends ParamsBag implements FactoryQueryBuilderContract
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
     * @var Builder|null
     */
    protected $query;

    /**
     * Nombre total d'éléments de la page courante.
     * @var int
     */
    protected $totalPage = 0;

    /**
     * Nombre total d'éléments en correspondance avec la requête.
     * @var int
     */
    protected $totalFounds = 0;

    /**
     * Nombre total de page d'éléments en correspondance avec la requête.
     * @var int|null
     */
    protected $totalPaged;

    /**
     * @inheritDoc
     */
    public function getColumns(): array
    {
        if (is_null($this->columns)) {
            $this->columns = $this->db()->getConnection()->getSchemaBuilder()->getColumnListing($this->db()->getTable())
                ? : [];
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
    public function db(): ?FactoryDb
    {
        return $this->factory->db();
    }

    /**
     * @inheritDoc
     */
    public function limitClause(): Builder
    {
        return $this->query()->forPage($this->pageNum, $this->perPage);
    }

    /**
     * @inheritDoc
     */
    public function orderClause(): Builder
    {
        if (!$this->orderby) {
            $this->orderby = $this->db()->getKeyName();
        }

        return $this->query()->orderBy($this->orderby, $this->order);
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
    public function parse(): FactoryQueryBuilderContract
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
    public function proceed(): iterable
    {
        $this->parse();
        $items = [];

        if ($this->db()) {
            $this->totalFounds = $this->whereClause()->count();
            $this->resetQuery();

            $this->whereClause();
            $this->orderClause();
            $this->limitClause();
            $items = $this->query()->get();

            $this->totalPage = $items->count();
            $this->totalPaged = ceil($this->totalFounds / $this->perPage);
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function resetQuery(): void
    {
        $this->query = null;
    }

    /**
     * @inheritDoc
     */
    public function query(): ?Builder
    {
        if (is_null($this->query)) {
            $this->query = $this->db() ? $this->db()::query() : null;
        }

        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function setPerPage(int $per_page): FactoryQueryBuilderContract
    {
        $this->perPage = $per_page > 0 ? $per_page : 0;

        return $this;
    }

    /**
     * Récupération du nombre total d'éléments sur la page courante.
     *
     * @return int
     */
    public function totalPage(): int
    {
        return (int)$this->totalPage;
    }

    /**
     * @inheritDoc
     */
    public function totalFounds(): int
    {
        return (int)$this->totalFounds;
    }

    /**
     * @inheritDoc
     */
    public function totalPaged(): int
    {
        return (int)$this->totalPaged;
    }

    /**
     * @inheritDoc
     */
    public function whereClause(): Builder
    {
        foreach ($this->all() as $k => $v) {
            if($this->hasColumn($k)) {
                is_array($v) ? $this->query()->whereIn($k, $v) : $this->query()->where($k, $v);
            }
        }

        return $this->query;
    }
}