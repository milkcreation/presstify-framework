<?php

namespace tiFy\Components\AdminView\ListTable\Column;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use tiFy\Components\AdminView\ListTable\Column\ColumnItemInterface;
use tiFy\Components\AdminView\ListTable\ListTableInterface;

class ColumnCollectionController implements ColumnCollectionInterface
{
    /**
     * Classe de rappel de la vue associée.
     * @var ListTableInterface
     */
    protected $app;

    /**
     * Liste des colonnes.
     * @var Collection|ColumnItemInterface[]
     */
    protected $columns = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array|object $item Données de l'élément courant.
     * @param ListTableInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct(ListTableInterface $app)
    {
        $this->app = $app;

        $this->columns = $this->parse($this->app->param('columns', []));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (isset($this->columns[$name])) :
            return $this->columns[$name];
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders($with_id = true)
    {
        return $this->columns->mapWithKeys(function($item, $key) use ($with_id){
            /** @var ColumnItemInterface $item */
            return [$key => $item->getHeader($with_id)];
        })->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getHidden()
    {
        return $this->columns
            ->filter(function($item, $key){
                /** @var ColumnItemInterface $item */
                return $item->isHidden();
            })
            ->pluck('name', null)
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getInfos()
    {
        $columns = $this->getList();
        $hidden = $this->getHidden();
        $sortable = $this->getSortable();
        $primary = $this->getPrimary();

        return [$columns, $hidden, $sortable, $primary];
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        return $this->columns->pluck('title', 'name')->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimary()
    {
        if (!$this->getList()) :
            return '';
        elseif ($primary = $this->columns->first(function ($item) {return $item['primary'] === true;})) :
            return $primary->getName();
        else :
            return $this->columns->first(function ($item) {return $item['name'] !== 'cb';})->getName();
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortable()
    {
        return $this->columns
            ->filter(function($item, $key){
                /** @var ColumnItemInterface $item */
                return $item->isSortable();
            })
            ->pluck('sortable', 'name')
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getVisible()
    {
        return $this->columns
            ->filter(function($item, $key){
                /** @var ColumnItemInterface $item */
                return !$item->isHidden();
            })
            ->pluck('name', null)
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function countVisible()
    {
        return count($this->getVisible());
    }

    /**
     * {@inheritdoc}
     */
    public function isPrimary($name)
    {
        return $name === $this->getPrimary();
    }

    /**
     * {@inheritdoc}
     */
    public function parse($columns = [])
    {
        $_columns = [];
        foreach ($columns as $name => $attrs) :
            if (is_numeric($name)) :
                $name = $attrs;
                $attrs = [
                    'title' => $name
                ];
            elseif (is_string($attrs)) :
                $attrs = [
                    'title' => $attrs
                ];
            endif;

            $controller = Arr::get($attrs, 'controller', ColumnItemController::class);

            if ($name === 'cb') :
                $controller = ColumnItemCbController::class;
            endif;

            $_columns[$name] = new $controller($name, $attrs, $this->app);
        endforeach;

        return new Collection($_columns);
    }
}