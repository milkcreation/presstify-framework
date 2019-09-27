<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\Order;

use tiFy\Metabox\MetaboxDriver;

class Order extends MetaboxDriver
{
    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => '_order',
            'title' => __('Ordre d\'affichage', 'tify')
        ]);
    }
}