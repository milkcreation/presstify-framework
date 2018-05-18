<?php

namespace tiFy\Components\Partial\Sidebar;

use tiFy\Components\Tools\Walkers\AbstractWalkerMenuTree;

class SidebarWalker extends AbstractWalkerMenuTree
{
    /**
     * {@inheritdoc}
     */
    public function openItems($item)
    {
        if (! $item->getDepth()) :
            return "\t\t\t\t<ul class=\"{$this->getOption('prefix')}Items {$this->getOption('prefix')}Items--open\">\n";
        else :
            return $this->getIndent($item->getDepth()) . "\t\t\t\t<ul class=\"{$this->getOption('prefix')}Items\">\n";
        endif;
    }
}