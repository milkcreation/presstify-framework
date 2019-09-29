<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\Icon;

use tiFy\Metabox\MetaboxDriver;

class Icon extends MetaboxDriver
{
    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'icon',
            'title' => __('Icône représentative', 'tify')
        ]);
    }
}