<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\TextRemaining;

use tiFy\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\Field;

class TextRemaining extends MetaboxDriver
{
    /**
     * @inheritDoc
     */
    public function content(): string
    {
        return (string)Field::get('text-remaining', array_merge($this->params(), [
            'name'  => $this->name(),
            'value' => $this->value()
        ]));
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'text_remaining',
            'title' => __('Extrait', 'tify')
        ]);
    }
}