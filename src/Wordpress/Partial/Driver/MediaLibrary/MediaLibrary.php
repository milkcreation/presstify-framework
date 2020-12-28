<?php declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Driver\MediaLibrary;

use tiFy\Contracts\Partial\PartialDriver as BasePartialDriverContract;
use tiFy\Wordpress\Contracts\Partial\MediaLibrary as MediaLibraryContract;
use tiFy\Wordpress\Partial\PartialDriver;

class MediaLibrary extends PartialDriver implements MediaLibraryContract
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        add_action('admin_enqueue_scripts', function () {
            @wp_enqueue_media();
        });
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            'button'  => [
                'tag'     => 'button',
                'content' => __('Ajouter un média', 'tify'),
            ],
            'options' => [
                'title'    => __('Sélectionner les fichiers à associer', 'tify'),
                'editing'  => true,
                'multiple' => true,
                'library'  => [],
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): BasePartialDriverContract
    {
        parent::parseParams();

        $this->set([
            'attrs.data-control'        => 'media-library',
            'attrs.data-options'        => $this->pull('options', []),
            'button.attrs.data-control' => 'media-library.open',
        ]);

        return $this;
    }
}