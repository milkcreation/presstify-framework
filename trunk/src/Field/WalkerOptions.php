<?php

namespace tiFy\Field;

use tiFy\Components\Tools\Walkers\AbstractWalkBase;

class WalkerOptions extends AbstractWalkBase
{
    /**
     * {@inheritdoc}
     */
    public function parseHtmlAttrs($attrs, $name)
    {
        if(!isset($attrs['id'])) :
            $attrs['id'] = "tiFyFieldOption-Item--{$name}";
        endif;

        if (!isset($attrs['class'])) :
            $attrs['class'] = "tiFyFieldOption-Item tiFyFieldOption--{$name}";
        endif;

        if(!isset($attrs['aria-current'])) :
            $attrs['aria-current'] = $this->isCurrent($name);
        endif;

        return $attrs;
    }

    /**
     * Récupération des attributs de la balise HTML d'un élément
     *
     * @param array $item Attribut de configuration de l'élément
     * @param int $depth Niveau de profondeur courant
     * @param string $parent Identifiant de qualification de l'élément parent courant
     *
     * @return string
     */
    public function getItemHtmlAttrs($item = null, $depth = 0, $parent = '')
    {
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

        $html_attrs = [];
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

        return implode(' ', $html_attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function openItems($item = null, $depth = 0, $parent = '')
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function closeItems($item = null, $depth = 0, $parent = '')
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function openItem($item = null, $depth = 0, $parent = '')
    {
        if ($item['group']) :
            return $this->getIndent($depth) . "<optgroup ". $this->getItemHtmlAttrs($item, $depth, $parent) . ">\n";
        else :
            return $this->getIndent($depth) . "<option ". $this->getItemHtmlAttrs($item, $depth, $parent) . ">\n";
        endif;

    }

    /**
     * {@inheritdoc}
     */
    public function closeItem($item = null, $depth = 0, $parent = '')
    {
        if ($item['group']) :
            return $this->getIndent($depth) . "</optgroup>\n";
        else :
            return $this->getIndent($depth) . "</option>\n";
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function contentItem($item = null, $depth = 0, $parent = '')
    {
        if (!$item['group']) :
            return ! empty($item['label']) ? esc_attr($item['label']) : '';
        endif;

        return '';
    }
}