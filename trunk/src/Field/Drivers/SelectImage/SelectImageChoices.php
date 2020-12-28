<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\SelectImage;

use Symfony\Component\Finder\Finder;
use tiFy\Field\Drivers\SelectJs\SelectJsChoices;
use tiFy\Field\Drivers\SelectImageDriverInterface;
use tiFy\Support\Img;

class SelectImageChoices extends SelectJsChoices implements SelectImageChoicesInterface
{
    /**
     * @param array|string $items
     * @param mixed $selected Liste des éléments selectionnés
     * @param SelectImageDriverInterface $field
     */
    public function __construct($items, $selected, SelectImageDriverInterface $field)
    {
        if (is_string($items)) {
            $finder = new Finder();
            $finder
                ->in($items)
                ->depth('== 0')
                ->files()
                ->name('/(\.ico$|\.gif$|\.jpe?g$|\.png$|\.svg$)/');

            $items = [];
            if ($field->get('none')) {
                $items[''] = Img::getBase64Src($field->fieldManager()->resources('/views/select-image/none.jpg'));
            }

            foreach ($finder as $file) {
                $items[$file->getRelativePathname()] = Img::getBase64Src($file->getRealPath());
            }
        }
        parent::__construct($items, $selected);
    }
}