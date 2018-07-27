<?php

namespace tiFy\Core\Templates\Admin\Model\XmlImport;

use tiFy\Core\Templates\Admin\Model\FileImport\FileImport;
use tiFy\Lib\Xml\Collection;
use tiFy\Lib\Xml\Reader;

class XmlImport extends FileImport
{
    /**
     * Balise XML à lire.
     *
     * @var string
     */
    protected $delimiterTagName = '';

    /**
     * Options du lecteur XML.
     *
     * @var array
     */
    protected $readerOptions = [];

    /**
     * Cartographie des données.
     *
     * @var array
     */
    protected $mapping = [
        'attrs' => [],
        'datas' => []
    ];

    /**
     * Colonnes de recherche.
     *
     * @var array
     */
    protected $search = [];

    /**
     * PARAMETRAGE
     */
    /**
     * Définition de la cartographie des paramètres autorisés
     *
     * @return array
     */
    public function set_params_map()
    {
        $params = parent::set_params_map();
        array_push($params, 'delimiterTagName', 'readerOptions', 'mapping', 'search');

        return $params;
    }

    /**
     * Définition de la balise XML à lire.
     *
     * @return string
     */
    public function set_delimiter_tag_name()
    {
        return '';
    }

    /**
     * Définition des options du lecteur XML.
     *
     * @return array
     */
    public function set_reader_options()
    {
        return [];
    }

    /**
     * Définition de la cartographie des données.
     *
     * @return array
     */
    public function set_mapping()
    {
        return [
            'attrs' => [],
            'datas' => []
        ];
    }

    /**
     * Définition des colonnes de recherche.
     *
     * @return array
     */
    public function set_search()
    {
        return [];
    }

    /**
     * Initialisation de la balise XML à lire.
     *
     * @return string
     */
    public function initParamdelimiterTagName()
    {
        return $this->delimiterTagName = $this->set_delimiter_tag_name();
    }

    /**
     * Initialisation des options du lecteur XML.
     *
     * @return array
     */
    public function initParamreaderOptions()
    {
        return $this->readerOptions = $this->set_reader_options();
    }

    /**
     * Initialisation de la cartographie des données.
     *
     * @return array
     */
    public function initParammapping()
    {
        return $this->mapping = $this->set_mapping();
    }

    /**
     * Initialisation des colonnes de recherche.
     *
     * @return array
     */
    public function initParamsearch()
    {
        return $this->search = $this->set_search();
    }

    /**
     * Récupération de la réponse.
     *
     * @todo Tri à tester.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getResponse()
    {
        $params = $this->parse_query_args();

        if (empty($params['filename']) || !$this->delimiterTagName) :
            return null;
        endif;

        try {
            $xml = new Reader($params['filename'], $this->delimiterTagName, $this->readerOptions);
            if ($this->mapping) :
                if (!empty($this->mapping['attrs'])) :
                    $xml->setAttrMapping($this->mapping['attrs']);
                endif;
                if (!empty($this->mapping['datas'])) :
                    $xml->setDataMapping($this->mapping['datas']);
                endif;
            endif;

            $xmlCollection = new Collection($xml);

            // Tri
            if (! empty($params['orderby'])) :
                $xmlCollection->sortBy($params['orderby']);
            endif;

            // Recherche
            if (!empty($params['search'])) :
                $xmlCollection->filter($params['search'], false, $this->search);
            endif;

            $xmlCollection->forPage(isset($params['paged']) ? (int)$params['paged'] : 1, $this->PerPage);

            foreach ($xmlCollection->all()->get() as $import_index => $item) :
                $item['_import_row_index'] = $import_index;
                $items[] = (object)$item;
            endforeach;

            $this->TotalItems = $xmlCollection->rewind()->all()->count();
            $this->TotalPages = ($this->PerPage > -1) ? (int)ceil($this->TotalItems / $this->PerPage) : 1;
        } catch (\Exception $e) {
            $items = [];
        };

        return $items;
    }
}