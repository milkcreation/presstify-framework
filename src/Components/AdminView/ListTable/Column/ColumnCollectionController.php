<?php

namespace tiFy\Components\AdminView\ListTable\Column;

use Illuminate\Support\Collection;
use tiFy\Components\AdminView\ListTable\Column\ColumnItemInterface;
use tiFy\AdminView\AdminViewInterface;

class ColumnCollectionController implements ColumnCollectionInterface
{
    /**
     * Classe de rappel de la vue associée.
     * @var AdminViewInterface
     */
    protected $view;

    /**
     * Liste des colonnes.
     * @var Collection|ColumnItemInterface[]
     */
    protected $columns = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $columns Liste des colonnes.
     * @param array|object $item Données de l'élément courant.
     * @param AdminViewInterface $view Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($columns, AdminViewInterface $view)
    {
        $this->view = $view;

        $this->columns = $this->parse($columns);
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
    public function getHidden()
    {
        return $this->columns
            ->filter(function($value, $key){
                return !empty($value['hidden']);
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
            ->filter(function($value, $key){
                return !empty($value['sortable']);
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
            ->filter(function($value, $key){
                return empty($value['hidden']);
            })
            ->pluck('name', null)
            ->all();
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

            $_columns[$name] = new ColumnItemController($name, $attrs, $this->view);
        endforeach;

        return new Collection($_columns);
    }
}