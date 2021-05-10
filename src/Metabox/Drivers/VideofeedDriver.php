<?php

declare(strict_types=1);

namespace tiFy\Metabox\Drivers;

use Pollen\Proxy\Proxies\Request;
use tiFy\Metabox\MetaboxDriver;

class VideofeedDriver extends MetaboxDriver implements VideofeedDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'videofeed';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'classes'   => [],
                'max'       => -1,
                'library'   => true,
                'removable' => true,
                'sortable'  => true,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Vidéos', 'tify');
    }

    /**
     * Définition d'un élément.
     *
     * @param int|string $index Indice de l'élément.
     * @param array $value Attributs de configuration de la vidéo.
     *
     * @return array
     */
    public function item($index, array $value = []): array
    {
        $name = $this->getName();
        $index = !is_numeric($index) ? $index : uniqid();

        $value = array_merge(
            [
                'poster' => '',
                'src'    => '',
            ],
            $value
        );
        $value['poster'] = ($img = wp_get_attachment_image_src($value['poster'])) ? $img[0] : $value['poster'];

        return [
            'index' => $index,
            'name'  => $this->get('max', -1) === 1 ? "{$name}[]" : "{$name}[{$index}]",
            'value' => $value,
        ];
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $defaultClasses = [
            'addnew'  => 'MetaboxVideofeed-addnew ThemeButton--primary ThemeButton--normal',
            'down'    => 'MetaboxVideofeed-itemSortDown ThemeFeed-itemSortDown',
            'input'   => 'MetaboxVideofeed-itemInput',
            'item'    => 'MetaboxVideofeed-item ThemeFeed-item',
            'items'   => 'MetaboxVideofeed-items ThemeFeed-items',
            'library' => 'MetaboxVideofeed-itemLibrary ThemeButton--secondary ThemeButton--small',
            'order'   => 'MetaboxVideofeed-itemOrder ThemeFeed-itemOrder',
            'remove'  => 'MetaboxVideofeed-itemRemove ThemeFeed-itemRemove',
            'sort'    => 'MetaboxVideofeed-itemSortHandle ThemeFeed-itemSortHandle',
            'up'      => 'MetaboxVideofeed-itemSortUp ThemeFeed-itemSortUp',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set(["classes.{$k}" => sprintf($this->get("classes.{$k}", '%s'), $v)]);
        }

        $this->set(
            [
                'attrs.class'        => sprintf($this->get('attrs.class', '%s'), 'MetaboxVideofeed'),
                'attrs.data-control' => 'metabox-videofeed',
            ]
        );

        if ($sortable = $this->get('sortable')) {
            $this->set(
                [
                    'sortable' => array_merge(
                        [
                            'placeholder' => 'MetaboxVideofeed-itemPlaceholder',
                            'axis'        => 'y',
                        ],
                        is_array($sortable) ? $sortable : []
                    ),
                ]
            );
        }

        $this->set(
            [
                'attrs.data-options' => [
                    'ajax'      => [
                        'data'     => [
                            'max'    => $this->get('max', -1),
                            'name'   => $this->getName(),
                            'viewer' => $this->getViewer(),
                        ],
                        'dataType' => 'json',
                        'method'   => 'post',
                        'url'      => $this->getXhrUrl(),
                    ],
                    'classes'   => $this->get('classes', []),
                    'library'   => $this->get('library'),
                    'removable' => $this->get('removable'),
                    'sortable'  => $this->get('sortable'),
                ],
            ]
        );
        if ($values = $this->getValue()) {
            $items = [];
            array_walk(
                $values,
                function ($value, $index) use (&$items) {
                    $items[] = $this->item($index, $value);
                }
            );
            $this->set('items', $items);
        }
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metaboxManager()->resources('/views/drivers/videofeed');
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        $index = Request::input('index');
        $max = Request::input('max', 0);

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre maximum de vidéos atteint.', 'tify'),
            ];
        } else {
            $this->setName(Request::input('name', ''));
            $this->setViewer(Request::input('viewer', []));
            $this->set(
                [
                    'max' => $max,
                ]
            );
            return [
                'success' => true,
                'data'    => $this->view('item-wrap', $this->item($index)),
            ];
        }
    }
}