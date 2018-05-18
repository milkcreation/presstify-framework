<?php

namespace tiFy\Field;

use tiFy\Components\Tools\Walkers\WalkerBaseController;

class WalkerOptions extends WalkerBaseController
{
    /**
     * Liste des options.
     * @var array {
     *
     *      @var string $indent Caractère d'indendation.
     *      @var int $start_indent Nombre de caractère d'indendation au départ.
     *      @var bool|string $sort Ordonnancement des éléments.false|true|append(défaut)|prepend.
     *      @var string $prefixe Préfixe de nommage des éléments HTML.
     * }
     */
    protected $options = [
        'indent'       => '\t',
        'start_indent' => 0,
        'sort'         => 'append',
        'prefix'       => 'tiFyFieldOption-'
    ];

    /**
     * Récupération des attributs de la balise HTML d'un élément
     *
     * @param array $item Attribut de configuration de l'élément
     * @param int $depth Niveau de profondeur courant
     * @param string $parent Identifiant de qualification de l'élément parent courant
     *
     * @return string
     */
    public function getItemHtmlAttrs($item)
    {
        /*
        $attrs = $item['attrs'];

        $attrs['id'] = "tiFyField-itemOption--{$item['id']}";
        
        $attrs['class'] = $this->getItemClass($item, $depth, $parent);

        if ($item['group']) :
            $attrs['label'] = !empty($item['label']) ? $item['label'] : $item['id'];
        else :
            $attrs['value'] = $item['value'];
            if (!is_null($this->getAttr('selected'))) :
                foreach($this->getAttr('selected', []) as $selected) :
                    if ($selected == $attrs['value']) :
                        array_push($attrs, 'selected');
                    endif;
                endforeach;
            endif;
        endif;
        */
        $html_attrs = [];
        /*
        foreach ($attrs as $k => $v) :
            if (is_array($v)) :
                $v = rawurlencode(json_encode($v));
            endif;
            if (is_int($k)) :
                $html_attrs[]= "{$v}";
            else :
                $html_attrs[]= "{$k}=\"{$v}\"";
            endif;
        endforeach;
        */
        return implode(' ', $html_attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function openItems($item)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function closeItems($item)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function openItem($item)
    {
        if ($item['group']) :
            return $this->getIndent($item->getDepth()) . "<optgroup ". $this->getItemHtmlAttrs($item) . ">\n";
        else :
            return $this->getIndent($item->getDepth()) . "<option ". $this->getItemHtmlAttrs($item) . ">\n";
        endif;

    }

    /**
     * {@inheritdoc}
     */
    public function closeItem($item)
    {
        if ($item['group']) :
            return $this->getIndent($item->getDepth()) . "</optgroup>\n";
        else :
            return $this->getIndent($item->getDepth()) . "</option>\n";
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function contentItem($item)
    {
        if (!$item['group']) :
            return ! empty($item['label']) ? esc_attr($item['label']) : '';
        endif;

        return '';
    }
}