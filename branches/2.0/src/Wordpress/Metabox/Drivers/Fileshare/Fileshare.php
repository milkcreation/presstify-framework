<?php declare(strict_types=1);

namespace tiFy\Wordpress\Metabox\Drivers\Fileshare;

use tiFy\Metabox\Drivers\Fileshare\Fileshare as BaseFileshare;

class Fileshare extends BaseFileshare
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        parent::boot();

        add_action('admin_enqueue_scripts', function() {
            @wp_enqueue_media();
        });
    }
}