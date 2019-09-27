<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\CustomHeader;

use tiFy\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\Field;

class CustomHeader extends MetaboxDriver
{
    /**
     * @inheritDoc
     */
    public function content(): string
    {
        return (string)Field::get('media-image', array_merge($this->params(), [
            'name' => $this->name(),
            'value' => $this->value()
        ]));
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            'media_library_title' => __('Personnalisation de l\'image d\'entête', 'tify'),
            'media_library_button' => __('Utiliser comme image d\'entête', 'tify'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'custom_header',
            'title' => __('Image d\'entête', 'tify')
        ]);
    }
}