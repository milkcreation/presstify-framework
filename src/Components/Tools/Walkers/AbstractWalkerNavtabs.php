<?php

namespace tiFy\Components\Tools\Walkers;

abstract class AbstractWalkerNavtabs extends WalkerBaseController
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
        'prefix'       => 'tiFyWalkerNavTabs-'
    ];

    /**
     * Fermeture d'un élement de navigation.
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
     */
    final protected function _closeNavItem($item)
    {
        return method_exists($this, 'closeNavItem_' . $item->getName())
            ? call_user_func([$this, 'closeNavItem_' . $item->getName()], $item)
            : call_user_func([$this, 'closeNavItem'], $item);
    }

    /**
     * Fermeture de la liste des éléments de navigation.
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
     */
    final protected function _closeNavItems($item)
    {
        return method_exists($this, 'closeNavItems_' . $item->getName())
            ? call_user_func([$this, 'closeNavItems_' . $item->getName()], $item)
            : call_user_func([$this, 'closeNavItems'], $item);
    }

    /**
     * Contenu d'un élément de navigation.
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
     */
    final protected function _contentNavItem($item)
    {
        return method_exists($this, 'contentNavItem_' . $item->getName())
            ? call_user_func([$this, 'contentNavItem_' . $item->getName()], $item)
            : call_user_func([$this, 'contentNavItem'], $item);
    }

    /**
     * Ouverture d'un élement de navigation
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
     */
    final protected function _openNavItem($item)
    {
        return method_exists($this, 'openNavItem_' . $item->getName())
            ? call_user_func([$this, 'openNavItem_' . $item->getName()], $item)
            : call_user_func([$this, 'openNavItem'], $item);
    }

    /**
     * Ouverture de la liste des éléments de navigation.
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
     */
    final protected function _openNavItems($item)
    {
        return method_exists($this, 'openNavItems_' . $item->getName())
            ? call_user_func([$this, 'openNavItems_' . $item->getName()], $item)
            : call_user_func([$this, 'openNavItems'], $item);
    }

    /**
     * Fermeture par défaut d'un élement de navigation.
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
     */
    public function closeNavItem($item)
    {
        $output = "";
        $output .= "</a>";
        $output .= $this->getIndent($item->getDepth()) . "\t</li>\n";

        return $output;
    }

    /**
     * Fermeture par défaut de la liste des éléments de navigation.
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
     */
    public function closeNavItems($item)
    {
        return $this->getIndent($item->getDepth()) . "</ul>\n";
    }

    /**
     * Contenu par défaut d'un élément de navigation.
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
     */
    public function contentNavItem($item)
    {
        return is_callable($item->getTitle())
            ? call_user_func_array($item->getTitle(), $item->getArgs())
            : $item->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function openItem($item)
    {
        return $this->getIndent($item->getDepth()) . "<div class=\"tab-pane {$this->getOption('prefix')}ContentItem {$this->getOption('prefix')}ContentItem--depth{$depth}\" id=\"{$item['name']}\" role=\"tabpanel\">\n";
    }

    /**
     * {@inheritdoc}
     */
    public function openItems($item)
    {
        return $this->getIndent($item->getDepth()) . "<div class=\"tab-content {$this->getOption('prefix')}ContentItems {$this->getOption('prefix')}ContentItems--depth{$depth}\">\n";
    }

    /**
     * Ouverture par défaut d'un élement de navigation.
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
     */
    public function openNavItem($item)
    {
        $output = "";
        $output .= $this->getIndent($item->getDepth()) . "\t<li id=\"{$this->getOption('prefix')}NavItem--{$item->getName()}\" class=\"{$this->getOption('prefix')}NavItem {$this->getOption('prefix')}NavItem--{$item->getName()}\" role=\"presentation\">\n";
        $output .= "<a href=\"#{$item->getName()}\" role=\"tab\" data-toggle=\"tab\">";

        return $output;
    }

    /**
     * Ouverture par défaut de la liste des éléments de navigation.
     *
     * @param WalkerItemBaseController $item Élément courant.
     *
     * @return string
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
    public function walk($items = [], $depth = 0, $parent = '')
    {
        $output = "";

        $sorted = $this->sort($items);

        $open = false;
        /** @var WalkerItemBaseController $item */
        foreach ($sorted as $item) :
            if ($item->getParent() !== $parent) :
                continue;
            endif;

            $item->setDepth($depth);

            if (!$open) :
                $open = $item;
                $output .= $this->_openNavItems($open);
            endif;

            $output .= $this->_openNavItem($item);
            $output .= $this->_contentNavItem($item);
            $output .= $this->_closeNavItem($item);

            $close = $item;
        endforeach;

        if ($open) :
            $output .= $this->_closeNavItems($close);
        endif;

        reset($sorted);

        $open = false;
        /** @var WalkerItemBaseController $item */
        foreach ($sorted as $item) :
            if ($item->getParent() !== $parent) :
                continue;
            endif;

            $item->setDepth($depth);

            if (!$open) :
                $open = $item;
                $output .= $this->_openItems($item);
                $opened = true;
            endif;

            $output .= $this->_openItem($item);
            $output .= $this->walk($items, ($depth + 1), $item->getName());
            $output .= $this->_contentItem($item);
            $output .= $this->_closeItem($item);

            $close = $item;
        endforeach;

        if ($open) :
            $output .= $this->_closeItems($close);
        endif;

        return $output;
    }
}