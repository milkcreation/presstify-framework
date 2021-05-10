<?php

declare(strict_types=1);

namespace tiFy\Metabox\Drivers;

use Pollen\Proxy\Proxies\Field;
use tiFy\Metabox\MetaboxDriver;

class SubtitleDriver extends MetaboxDriver implements SubtitleDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'subtitle';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'attrs' => [
                    'class'       => 'widefat',
                    'placeholder' => __('Sous-titre', 'tify'),
                    'style'       => 'margin-top:10px;margin-bottom:20px;background-color:#fff;font-size:1.4em;' .
                        'height:1.7em;line-height:100%;margin:10 0 15px;outline:0 none;padding:3px 8px;width:100%;',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Sous-titre', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return Field::get(
            'text',
            array_merge(
                $this->all(),
                [
                    'name'  => $this->getName(),
                    'value' => $this->getValue(),
                ]
            )
        )->render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metaboxManager()->resources('/views/drivers/subtitle');
    }
}