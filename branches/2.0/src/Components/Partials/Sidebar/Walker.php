<?php

namespace tiFy\Components\Partials\Sidebar;

use tiFy\Components\Tools\Walkers\AbstractWalkMenuTree;

class Walker extends AbstractWalkMenuTree
{
    /**
     * {@inheritdoc}
     */
    public function parseHtmlAttrs($attrs, $name)
    {
        if(!isset($attrs['id'])) :
            $attrs['id'] = "tiFySidebar-Node--{$name}";
        endif;

        if (!isset($attrs['class'])) :
            $attrs['class'] = "tiFySidebar-Node tiFySidebar-Node--{$name}";
        endif;

        if(!isset($attrs['aria-current'])) :
            $attrs['aria-current'] = $this->isCurrent($name);
        endif;

        return $attrs;
    }

    /**
     * {@inheritdoc}
     */
    public function openItems($item = null, $depth = 0, $parent = '')
    {
        if (! $depth) :
            return "\t\t\t\t<ul class=\"tiFySidebar-Items tiFySidebar-Items--open\">\n";
        else :
            return $this->getIndent($depth) . "\t\t\t\t<ul class=\"tiFySidebar-items\">\n";
        endif;
    }
}