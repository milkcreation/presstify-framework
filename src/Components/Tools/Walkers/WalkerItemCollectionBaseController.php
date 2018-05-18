<?php

namespace tiFy\Components\Tools\Walkers;

use Illuminate\Support\Collection;
use tiFy\Kernel\Tools;

class WalkerItemCollectionBaseController extends Collection
{
    /**
     * Classe de rappel du controleur principal
     * @var WalkerBaseController
     */
    protected $walker;

    /**
     * Liste de éléments afficher dans le walker.
     * @var array
     */
    protected $items = [];

    /**
     * Liste des noms de qualification unique des élément à traiter.
     * @var array
     */
    protected $names = [];

    /**
     * Nom de qualification de l'élement.
     * @var string
     */
    protected $current = '';

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($items, $walker)
    {
        $this->walker = $walker;
        $this->parse($items);

        parent::__construct($this->items);
    }

    /**
     * Traitement de liste des éléments.
     *
     * @return array
     */
    protected function parse($items)
    {
        foreach ($items as $attrs) :
            $name =  $this->uniqName($attrs);
            unset($attrs['name']);

            $this->items[] = new WalkerItemBaseController($name, $attrs, $this->walker);
        endforeach;
    }

    /**
     * Génération alétoire d'un nom de qualification unique.
     *
     * @param array $item Élément à nommer.
     * @param int $index Indice de nommage de l'élément.
     *
     * @return string
     */
    public function uniqName($item, $index = 0)
    {
        $item['name'] = isset($item['name']) ? $item['name'] : uniqid();

        if (in_array($item['name'], $this->names)) :
            $item['name'] = $item['name'] . '-' . $index++;
            return $this->uniqName($item, $index);
        endif;

        array_push($this->names, $item['name']);

        return $item['name'];
    }
}