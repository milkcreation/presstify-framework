<?php

namespace tiFy\Lib\Xml;

use Illuminate\Support\Collection as ICollection;
use tiFy\Lib\Xml\Reader;

/**
 * Gestion d'une collection d'éléments XML.
 *
 * @todo À agrémenter au besoin.
 * @see https://laravel.com/docs/5.6/collections#available-methods
 *
 * @package SedeaPro\SageImport
 */
class Collection
{
    /**
     * Lecteur XML.
     *
     * @var XmlReader
     */
    protected $xmlReader = null;

    /**
     * Collection d'éléments XML.
     *
     * @var ICollection
     */
    protected $collection = null;

    /**
     * Éléments XML.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Constructeur.
     *
     * @param Reader $xmlReader Lecteur XML.
     *
     * @return void
     */
    public function __construct(Reader $xmlReader)
    {
        $this->xmlReader = $xmlReader;
    }

    /**
     * Récupération de tous les éléments XML.
     *
     * @return $this
     */
    public function all()
    {
        $this->items = $this->setItems()->collection->values()->toArray();

        return $this;
    }

    /**
     * Filtrage des éléments XML selon une paire clé/valeur.
     *
     * @param string $key Clé à filtrer.
     * @param mixed $value Valeur à filtrer.
     *
     * @return $this
     */
    public function where($key = '', $value)
    {
        $this->items = $this->setItems()->collection->where($key, $value)->values()->toArray();

        return $this;
    }

    /**
     * Tri des éléments XML selon une clé.
     *
     * @param string $by Clé à trier.
     *
     * @return $this
     */
    public function sortBy($by = '')
    {
        $this->items = $this->setItems()->collection->sortBy($by)->values()->toArray();

        return $this;
    }

    /**
     * Filtrage des éléments XML selon une valeur à rechercher dans des colonnes fournies en paramètre.
     *
     * @todo Recherche type regex %%
     *
     * @param mixed $needle Valeur à filtrer.
     * @param bool $strict Type de comparaison.
     * @param array $searchColumns Colonnes dans lesquelles la valeur doit être recherchée.
     *
     * @return $this
     */
    public function filter($needle, $strict = false, $searchColumns = [])
    {
        $recursiveFilter = function ($needle, $haystack, $strict, $searchColumns) use (&$recursiveFilter) {
            foreach ($haystack as $itemKey => $itemVal) :
                if ($searchColumns) :
                    if (in_array($itemKey, $searchColumns)) :
                        $found = is_array($itemVal) ? in_array($needle, $itemVal, $strict) : ($strict ? ($needle === $itemVal) : ($needle == $itemVal));
                    elseif (is_array($itemVal)) :
                        $found = $recursiveFilter($needle, $itemVal, $strict, $searchColumns);
                    endif;
                else :
                    $found = is_array($itemVal) ? $recursiveFilter($needle, $itemVal, $strict, $searchColumns) : ($strict ? ($needle === $itemVal) : ($needle == $itemVal));
                endif;
                if (isset($found) && $found) :
                    return true;
                else:
                    continue;
                endif;
            endforeach;

            return false;
        };

        $this->items = $this->setItems()->collection->filter(function ($item, $key) use (&$recursiveFilter, $searchColumns, $needle, $strict) {
            return $recursiveFilter($needle, $item, $strict, $searchColumns);
        });

        return $this;
    }

    /**
     * Récupération des éléments XML selon une pagination définit.
     *
     * @param int $pageNumber Numéro de la page.
     * @param int $perPage Nombre d'éléments par page.
     *
     * @return $this
     */
    public function forPage($pageNumber = 1, $perPage = -1)
    {
        if ($perPage < 0) :
            $this->all();
        else :
            $this->items = $this->setItems()->collection->forPage($pageNumber, $perPage)->values()->toArray();
        endif;

        return $this;
    }

    /**
     * Récupération du nombre d'éléments XML de la collection.
     *
     * @return int
     */
    public function count()
    {
        return $this->collection->count();
    }

    /**
     * Définition des éléments XML.
     *
     * @return $this
     */
    private function setItems()
    {
        $this->collection = !empty($this->items) ? new ICollection($this->items) : new ICollection(iterator_to_array($this->xmlReader));

        return $this;
    }

    /**
     * Remise à zéro des éléments XML.
     *
     * @return $this
     */
    public function rewind()
    {
        $this->collection = new ICollection(iterator_to_array($this->xmlReader));
        $this->items = [];

        return $this;
    }

    /**
     * Récupération des éléments XML.
     *
     * @return array
     */
    public function get()
    {
        return $this->items;
    }
}