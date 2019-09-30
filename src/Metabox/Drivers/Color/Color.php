<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\Color;

use tiFy\Metabox\MetaboxDriver;

class Color extends MetaboxDriver
{
    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'color',
            'title' => __('Couleur', 'tify')
        ]);
    }
}