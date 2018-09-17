<?php

namespace tiFy\Partial\Navtabs;

use tiFy\Kernel\Walker\AbstractWalkerNavtabs;

class Walker extends AbstractWalkerNavtabs
{
    /**
     * {@inheritdoc}
     */
    public function openNavItems($item)
    {
        switch ($item->getDepth()) :
            case 0 :
                $class = 'nav nav-stacked';
                break;
            case 1 :
                $class = 'nav nav-tabs';
                break;
            case 2 :
                $class = 'nav nav-pills';
                break;
        endswitch;

        return $this->getIndent($item->getDepth()) . "<ul class=\"{$this->getOption('prefix')}NavItems {$this->getOption('prefix')}NavItems--depth{$item->getDepth()} {$class}\" role=\"tablist\">\n";
    }

    /**
     * {@inheritdoc}
     */
    public function openItems($item)
    {
        return $this->getIndent($item->getDepth()) . "<div class=\"{$this->getOption('prefix')}ContentItems {$this->getOption('prefix')}ContentItems--depth{$item->getDepth()} tab-content\">\n";
    }

    /**
     * {@inheritdoc}
     */
    public function openItem($item)
    {
        return $this->getIndent($item->getDepth()) . "<div id=\"{$item->getName()}\" class=\"{$this->getOption('prefix')}ContentItem {$this->getOption('prefix')}ContentItem--depth{$item->getDepth()} tab-pane\" role=\"tabpanel\">\n";
    }
}