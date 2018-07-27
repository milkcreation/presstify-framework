<?php

namespace tiFy\Lib\Xml;

use XmlIterator\XmlIterator;

class Reader extends XmlIterator
{
    /**
     * Cartographie des attributs.
     *
     * @var array
     */
    protected $attrMapping = [];

    /**
     * Cartographie des données.
     *
     * @var array
     */
    protected $dataMapping = [];

    /**
     * Constructeur.
     *
     * @param string $file_path Chemin absolu|URI du fichier XML.
     * @param string $delimiter_tag_name Balise XML à lire.
     * @param array $reader_opts Options du lecteur XML.
     *
     * @throws \Exception
     */
    public function __construct($file_path = '', $delimiter_tag_name = '', $reader_opts = [])
    {
        if (!file_exists($file_path)) :
            throw new \Exception(__('Le fichier n\'existe pas', 'theme'));
        endif;
        if (!in_array(mime_content_type($file_path), ['application/xml', 'text/xml'])) :
            throw new \Exception(__('Ce type de fichier n\'est pas pris en charge.', 'theme'));
        endif;

        parent::__construct($file_path, $delimiter_tag_name, $reader_opts);
    }

    /**
     * Définition de la cartographie des attributs.
     *
     * @param array $attr_mapping Cartographie des attributs.
     *
     * @return $this
     */
    public function setAttrMapping($attr_mapping = [])
    {
        $this->attrMapping = $attr_mapping;

        return $this;
    }

    /**
     * Définition de la cartographie des données.
     *
     * @param array $data_mapping Cartographie des données.
     *
     * @return $this
     */
    public function setDataMapping($data_mapping = [])
    {
        $this->dataMapping = $data_mapping;

        return $this;
    }

    /**
     * Return the current element, <b>FALSE</b> on error
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @link http://stackoverflow.com/a/1835324/372654
     *
     * @return false|array|\SimpleXMLElement
     */
    public function current()
    {
        $current = parent::current();

        // Définition des attributs.
        $attrs = ($this->attrMapping && !empty($current['@attributes'])) ? $this->map($this->attrMapping, $current['@attributes']) : [];
        // Définition des données.
        $datas = $this->dataMapping ? $this->map($this->dataMapping, $current) : [];

        if (!empty($attrs) && !empty($datas)) :
            $current = array_merge($attrs, $datas);
        elseif (!empty($attrs)) :
            $current = $attrs;
        elseif (!empty($datas)) :
            $current = $datas;
        endif;

        return $current;
    }

    /**
     * Définition de la cartographie d'un type données sur un élément.
     *
     * @param array $mapping Cartographie du type de données.
     * @param array $haystack Élement courant.
     *
     * @return array
     */
    public function map($mapping = [], $haystack = [])
    {
        $final = [];

        $walk = function ($item, $key) use (&$walk, &$final, $mapping) {
            if (is_array($item)) :
                if (!$this->fill($item, $key, $mapping, $final)) :
                    return array_walk($item, $walk);
                endif;
            else :
                $this->fill($item, $key, $mapping, $final);
            endif;

            return $item;
        };

        array_walk(
            $haystack,
            $walk,
            $mapping
        );

        return $final;
    }

    /**
     * Hydratation d'un élément cartographié.
     *
     * @param mixed $item Valeur de l'élément.
     * @param mixed $key Index de l'élement.
     * @param array $mapping Cartographie du type de données.
     * @param array $final Tableau à hydrater.
     *
     * @return bool
     */
    protected function fill($item, $key, $mapping = [], &$final = [])
    {
        if (is_numeric($key)) :
            return false;
        endif;

        if (isset($mapping[$key])) :
            return $final[$mapping[$key]] = $item;
        elseif (!is_bool(array_search($key, $mapping))) :
            return $final[$key] = $item;
        endif;

        return false;
    }
}