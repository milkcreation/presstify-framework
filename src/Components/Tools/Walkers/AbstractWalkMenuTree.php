<?php

namespace tiFy\Components\Tools\Walkers;

use tiFy\Components\Tools\Walkers\AbstractWalkBase;

abstract class AbstractWalkMenuTree extends AbstractWalkBase
{
    /**
     * {@inheritdoc}
     */
    public function parseHtmlAttrs($attrs, $name)
    {
        if(!isset($attrs['id'])) :
            $attrs['id'] = "tiFyWalkerMenuTree-Item--{$name}";
        endif;

        if (!isset($attrs['class'])) :
            $attrs['class'] = "tiFyWalkerMenuTree-contentItem tiFyWalkerMenuTree-Item--{$name}";
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
        return $this->getIndent($depth) . "\t\t<ul class=\"tiFyWalkerMenuTree-Items tiFyWalkerMenuTree-Items--{$depth}\">\n";
    }

    /**
     * {@inheritdoc}
     */
    public function closeItems($item = null, $depth = 0, $parent = '')
    {
        return $this->getIndent($depth) . "\t\t</ul>\n";
    }

    /**
     * {@inheritdoc}
     */
    public function openItem($item = null, $depth = 0, $parent = '')
    {        
        return $this->getIndent($depth) . "\t<li ". $this->getHtmlAttrs($item['name']) .">\n";
    }

    /**
     * {@inheritdoc}
     */
    public function closeItem($item = null, $depth = 0, $parent = '')
    {
        return $this->getIndent($depth) . "\t</li>\n";
    }
}