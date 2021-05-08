<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Metabox\Drivers;

use tiFy\Metabox\Drivers\ImagefeedDriver as BaseImagefeedDriver;
use tiFy\Metabox\MetaboxDriverInterface;

class ImagefeedDriver extends BaseImagefeedDriver
{
    /**
     * @inheritDoc
     */
    public function boot(): MetaboxDriverInterface
    {
        parent::boot();

        add_action(
            'admin_enqueue_scripts',
            function () {
                @wp_enqueue_media();
            }
        );
        return $this;
    }
}
