<?php

namespace tiFy\Field\SelectImage;

use Symfony\Component\Finder\Finder;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Field\SelectJs\SelectJsChoices;
use tiFy\Kernel\Tools;

class SelectImageChoices extends SelectJsChoices
{
    /**
     * Liste des éléments.
     * @var SelectImageChoice[]
     */
    protected $items = [];

    /**
     * Instance du controleur de gestion des gabarits d'affichage.
     * @var ViewEngine
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param array|string $items
     * @param ViewEngine $viewer
     * @param mixed $selected Liste des éléments selectionnés
     */
    public function __construct($items, ViewEngine $viewer, $selected = null)
    {
        $this->viewer = $viewer;

        if (is_string($items)) :
            $finder = new Finder();
            $finder->in($items)->depth('== 0')->name('(.ico|.gif|jpe?g|.png|.svg)');
            $items = [];
            foreach ($finder as $file) :
                $items[$file->getRelativePathname()] = "<img src=\"" . Tools::File()->imgBase64Src($file->getRealPath()) . "\" />";
            endforeach;
        else :
            foreach($items as &$item) :
                if(is_string($item) && validator()->isUrl($item)) :
                    $item = "<img src=\"" . $item . "\"/>";
                endif;
            endforeach;
        endif;

        parent::__construct($items, $viewer, $selected);
    }

    /**
     * {@inheritdoc}
     */
    public function wrap($name, $item)
    {
        if (!$item instanceof SelectImageChoice) :
            $item = new SelectImageChoice($name, $item, $this->viewer);
        endif;

        return $this->items[] = $item;
    }
}