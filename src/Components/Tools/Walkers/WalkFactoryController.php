<?php

namespace tiFy\Components\Tools\Walkers;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use tiFy\Kernel\Tools;

class WalkFactoryController extends Collection
{
    /**
     * Liste de éléments à traiter.
     * @var array
     */
    protected $items = [];

    /**
     * Liste des attributs par défaut d'un élément.
     * @var array {
     *      @var string $name Nom de qualification de l'élément.
     *      @var  string $parent Nom de qualification de l'élément parent.
     *      @var string|callable Contenu de l'élément.
     *      @var array $attrs Attributs HTML de la balise du conteneur de l'élément.
     *      @var int $position Ordre d'affichage de l'élément dans le parent associé.
     * }
     */
    protected $defaults = [
        'name'     => '',
        'parent'   => '',
        'content'  => '',
        'attrs'    => '',
        'position' => 0,
    ];

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
     * Caractère d'indendation.
     * @var string Chaine de caractére d'occurence "\t"
     */
    protected $indent = "\t";

    /**
     * Nombre de caractère d'indendation au départ.
     * @var int
     */
    protected $startIndent = 0;

    /**
     * Liste des options.
     * @var array
     */
    protected $options = [];

    /**
     * Type d'ordonnancement des éléments.
     * @var bool|string false|true|append|prepend
     */
    protected $sortType = 'append';

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items Liste de éléments à traiter.
     * @parma array $options Liste des options de traitement.
     *
     * @return void
     */
    public function __construct($items = [], $options = [])
    {
        $this->items = array_merge($this->items, $items);
        $this->_parseItems();

        $this->options = $options;

        parent::__construct($this->items);
    }

    /**
     * Récupération static de l'affichage.
     *
     * @param array $items Liste de éléments à traiter.
     * @parma array $options Liste des options de traitement.
     *
     * @return string
     */
    public static function display($items = null, $options = [])
    {
        $self = new static($items, $options);

        return (string)$self;
    }

    /**
     * Fermeture d'un élement.
     *
     * @param array $item Élément courant.
     * @param int $depth Niveau de profondeur courant.
     * @param string $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    private function _closeItem($item = null, $depth = 0, $parent = '')
    {
        return method_exists($this, 'closeItem_' . $item['name'])
            ? call_user_func([$this, 'closeItem_' . $item['name']], $item, $depth, $parent)
            : call_user_func([$this, 'closeItem'], $item, $depth, $parent);
    }

    /**
     * Fermeture d'une liste d'éléments
     *
     * @param array $item Élément courant.
     * @param int $depth Niveau de profondeur courant.
     * @param string $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    private function _closeitems($item = null, $depth = 0, $parent = '')
    {
        return method_exists($this, 'closeItems_' . $item['name'])
            ? call_user_func([$this, 'closeItems_' . $item['name']], $item, $depth, $parent)
            : call_user_func([$this, 'closeItems'], $item, $depth, $parent);
    }

    /**
     * Rendu d'un élément
     *
     * @param array $item Élément courant.
     * @param int $depth Niveau de profondeur courant.
     * @param string $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    private function _contentItem($item = null, $depth = 0, $parent = '')
    {
        return method_exists($this, 'contentItem_' . $item['name'])
            ? call_user_func([$this, 'contentItem_' . $item['name']], $item, $depth, $parent)
            : call_user_func([$this, 'contentItem'], $item, $depth, $parent);
    }

    /**
     * Ouverture d'un élement.
     *
     * @param array $item Élément courant.
     * @param int $depth Niveau de profondeur courant.
     * @param string $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    private function _openItem($item = null, $depth = 0, $parent = '')
    {
        return method_exists($this, 'openItem_' . $item['name'])
            ? call_user_func([$this, 'openItem_' . $item['name']], $item, $depth, $parent)
            : call_user_func([$this, 'openItem'], $item, $depth, $parent);
    }

    /**
     * Ouverture d'une liste d'éléments.
     *
     * @param array $item Élément courant.
     * @param int $depth Niveau de profondeur courant.
     * @param string $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    private function _openItems($item = null, $depth = 0, $parent = '')
    {
        return method_exists($this, 'openItems_' . $item['name'])
            ? call_user_func([$this, 'openItems_' . $item['name']], $item, $depth, $parent)
            : call_user_func([$this, 'openItems'], $item, $depth, $parent);
    }

    /**
     * Traitement de liste des éléments.
     *
     * @return array
     */
    private function _parseItems()
    {
        foreach ($this->items as &$item) :
            $item['name'] =  $this->generateUniqName($item);

            $item = array_merge(
                $this->defaults,
                $item
            );
        endforeach;
    }

    /**
     * Fermeture par défaut d'un contenu d'élement
     *
     * @param array $item Élément courant.
     * @param int $depth Niveau de profondeur courant.
     * @param string $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    public function closeItem($item = null, $depth = 0, $parent = '')
    {
        return $this->getIndent($depth) . "</div>\n";
    }

    /**
     * Fermeture d'une liste d'éléments.
     *
     * @param array $item Élément courant.
     * @param int $depth Niveau de profondeur courant.
     * @param string $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    public function closeItems($item = null, $depth = 0, $parent = '')
    {
        return $this->getIndent($depth) . "</div>\n";
    }

    /**
     * Rendu par défaut d'un contenu d'élément
     *
     * @param array $item Élément courant.
     * @param int $depth Niveau de profondeur courant.
     * @param string $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    public function contentItem($item = null, $depth = 0, $parent = '')
    {
        return is_callable($item['content'])
            ? call_user_func_array($item['content'], $item)
            : (!empty($item['content']) ? $item['content'] : '');
    }

    /**
     * Génération alétoire d'un nom de qualification unique.
     *
     * @param array $item Élément à traiter.
     * @param int $index Indice de nommage de l'élément.
     *
     * @return string
     */
    public function generateUniqName($item, $index = 0)
    {
        $item['name'] = isset($item['name']) ? $item['name'] : uniqid();

        if (in_array($item['name'], $this->names)) :
            $item['name'] = $item['name'] . '-' . $index++;
            return $this->generateUniqName($item, $index);
        endif;

        array_push($this->names, $item['name']);

        return $item['name'];
    }

    /**
     * Récupération d'attribut d'un élément.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param string $key Clé d'indexe de l'attribut à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getAttr($name, $key, $default = null)
    {
        if (!$attrs = $this->getItem($name)) :
            return $default;
        endif;

        Arr::get($attrs, $key, $default);
    }

    /**
     * Récupération de la liste des attributs de balise HTML
     *
     * @param string $name Nom de qualification de l'élément.
     *
     * @return string
     */
    public function getHtmlAttrs($name)
    {
        $html_attrs = $this->parseHtmlAttrs($this->getAttr($name, 'attrs'), $name);

        return Tools::Html()->parseAttrs($html_attrs);
    }

    /**
     * Récupération d'un élément.
     *
     * @param string $name Nom de qualification de l'élément.
     *
     * @return string
     */
    public function getItem($name)
    {
        $key = $this->search(function ($item) use ($name) {
            return $item['name'] === $name;
        });

        if ($key !== false) :
            return $this->get($key);
        endif;
    }

    /**
     * Récupération de l'indentation d'un élément.
     *
     * @param int $depth Niveau de profondeur de l'élément.
     *
     * @return string
     */
    public function getIndent($depth = 0)
    {
        return str_repeat($this->indent, $depth + $this->startIndent);
    }

    /**content
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Vérification si l'élément possède au moins un enfant.
     *
     * @param string $name Nom de qualification de l'élément.
     *
     * @return bool
     */
    public function hasChild($name)
    {
        return array_search($name, array_column($this->items, 'parent'));
    }

    /**
     * Vérification si un élément hérite d'un parent.
     *
     * @param string $name Nom de qualification de l'élément à vérifier.
     *
     * @return bool
     */
    public function hasParent($name)
    {
        return $this->getAttr($name, 'parent') ? true : false;
    }

    /**
     * Vérification si un élément est l'élément courant.
     *
     * @param string $name Nom de qualification de l'élément.
     *
     * @return bool
     */
    public function isCurrent($name)
    {
        if (!$this->current) :
            return $this->current === $name;
        endif;
    }

    /**
     * Ouverture d'une liste d'éléments.
     *
     * @param array $item Élément courant.
     * @param int $depth Niveau de profondeur courant.
     * @param string $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    public function openItem($item = null, $depth = 0, $parent = '')
    {
        return $this->getIndent($depth) . "<div " . $this->getHtmlAttrs($item['name']) . ">\n";
    }

    /**
     * Ouverture par défaut d'une liste de contenus d'éléments
     *
     * @param array $item Attribut de configuration de l'élément
     * @param int $depth Niveau de profondeur courant
     * @param string $parent Identifiant de qualification de l'élément parent courant
     *
     * @return string
     */
    public function openItems($item = null, $depth = 0, $parent = '')
    {
        return $this->getIndent($depth) . "<div class=\"tiFyWalker-Items tiFyWalker-Items--{$depth}\">\n";
    }

    /**
     * Traitement de la liste des attributs de balise HTML.
     *
     * @param array $attrs Liste des attributs de balise HTML.
     * @param string $name Nom de qualification de l'élément.
     *
     * @return array
     */
    public function parseHtmlAttrs($attrs, $name)
    {
        if(!isset($attrs['id'])) :
            $attrs['id'] = "tiFyWalker-Item--{$name}";
        endif;

        if (!isset($attrs['class'])) :
            $attrs['class'] = "tiFyWalker-Item tiFyWalker-Item--{$name}";
        endif;

        return $attrs;
    }

    /**
     * Définition de l'élément courant.
     *
     * @param string $name Nom de qualification de l'élément.
     *
     * @return void
     */
    public function setCurrent($name)
    {
        $this->current = $name;
    }

    /**
     * Ordonnancement des éléments.
     *
     * @param $items Liste des éléments à traiter.
     * @param $depth Niveau de profondeur courant.
     * @param $parent Nom de qualification de l'élément parent courant.
     *
     * @return array
     */
    public function sort($items = [], $depth = 0, $parent = '')
    {
        if( !$this->sortType) :
            return $items;
        endif;

        $positions = [];

        // Extraction des données de position des éléments courants
        foreach ($items as $k => $item) :
            if ($parent !== $item['parent']) :
                continue;
            endif;
            $positions[$k] = isset($item['position']) ? $item['position'] : null;
        endforeach;

        // Bypass - Aucun élément à traiter
        if (empty($positions)) :
            return [];
        endif;

        // Récupération des informations de position
        $max = max($positions);
        $min = ($positions);
        $count = count($positions);
        $i = 1;
        $sorted = [];

        foreach ($positions as $k => $position) :
            if (is_null($position)) :
                switch ($this->sortType) :
                    default :
                    case 'append' :
                        $position = $max++;
                        break;
                    case 'prepend' :
                        $position = $min++ - $count;
                        break;
                endswitch;
            endif;

            if (isset($sorted[$position])) :
                switch ($this->sortType) :
                    default :
                    case 'append' :
                        $position = (float)$position . "." . $i++;
                        break;
                    case 'prepend' :
                        $position = (float)($position - 1) . "." . (99999 - ($count + $i++));
                        break;
                endswitch;
            endif;

            $sorted[$position] = $items[$k];
        endforeach;

        ksort($sorted);

        return $sorted;
    }

    /**
     * Itérateur d'affichage.
     *
     * @param $items Liste des éléments à traiter.
     * @param $depth Niveau de profondeur courant.
     * @param $parent Nom de qualification de l'élément parent courant.
     *
     * @return string
     */
    public function walk($items = [], $depth = 0, $parent = '')
    {
        $output = "";

        // Ordonnancement des éléments
        $sorted = $this->sort($items, $depth, $parent);

        // Contenus des onglets
        $opened = false;
        foreach ($sorted as $item) :
            if ($parent !== $item['parent']) :
                continue;
            endif;

            if (!$opened) :
                $output .= $this->_openItems($item, $depth, $parent);
                $opened = true;
            endif;

            $output .= $this->_openItem($item, $depth, $parent);
            $output .= $this->_contentItem($item, $depth, $parent);
            $output .= $this->walk($items, ($depth + 1), $item['name']);
            $output .= $this->_closeItem($item, $depth, $parent);

            $prevDepth = $depth;
        endforeach;

        if ($opened) :
            $output .= $this->_closeItems(null, $depth, $parent);
        endif;

        return $output;
    }

    /**
     * Récupération de l'affichage du controleur depuis l'instance.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->walk($this->items);
    }
}