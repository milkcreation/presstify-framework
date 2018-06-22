<?php

namespace tiFy\Components\AdminView\ListTable\Filter;

use Illuminate\Support\Collection;
use tiFy\Components\AdminView\ListTable\Filter\FilterItemController;
use tiFy\AdminView\AdminViewInterface;

class FilterCollectionController
{
    /**
     * Classe de rappel de la vue associée.
     * @var AdminViewInterface
     */
    protected $view;

    /**
     * Liste des filtres.
     * @var void|FilterItemController[]
     */
    protected $filters = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $filters Liste des filtres.
     * @param AdminViewInterface $view Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($filters, AdminViewInterface $view)
    {
        $this->view = $view;

        $this->filters = $this->parse($filters);
    }

    /**
     * Récupération de la liste des filtres.
     *
     * @return array
     */
    public function all()
    {
        return $this->filters;
    }

    /**
     * Traitement de la liste des filtres.
     *
     * @param array $filters Liste des filtres.
     *
     * @return void
     */
    public function parse($filters = [])
    {
        $_filters = [];
        foreach ($filters as $name => $attrs) :
            if (is_numeric($name)) :
                $_filters[$attrs] = new FilterItemController($attrs, [], $this->view);
            elseif (is_string($attrs)) :
                $_filters[$name] = $attrs;
            else :
                $_filters[$name] = new FilterItemController($name, $attrs, $this->view);
            endif;
        endforeach;

        $_filters = array_filter($_filters, function ($value) {
            return (string)$value !== '';
        });

        return $_filters;
    }
}