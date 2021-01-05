<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Metabox\Drivers;

use tiFy\Metabox\MetaboxDriverInterface;
use tiFy\Metabox\Drivers\FilefeedDriver as BaseFilefeedDriver;

class FilefeedDriver extends BaseFilefeedDriver
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
