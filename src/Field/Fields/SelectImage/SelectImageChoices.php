<?php

namespace tiFy\Field\Fields\SelectImage;

use Symfony\Component\Finder\Finder;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Field\Fields\SelectJs\SelectJsChoices;
use tiFy\Kernel\Tools;

class SelectImageChoices extends SelectJsChoices
{
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
            $finder
                ->in($items)
                ->depth('== 0')
                ->files()
                ->name('#(\.ico$|\.gif$|\.jpe?g$|\.png$|\.svg$)#');
            $items = [];
            foreach ($finder as $file) :
                $items[$file->getRelativePathname()] = Tools::File()->imgBase64Src($file->getRealPath());
            endforeach;
        endif;

        parent::__construct($items, $viewer, $selected);
    }
}