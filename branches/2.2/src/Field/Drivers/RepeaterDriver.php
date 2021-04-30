<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;
use tiFy\Support\Arr;
use tiFy\Support\Proxy\Request;

class RepeaterDriver extends FieldDriver implements RepeaterDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                /**
                 * @var array $ajax Liste des arguments de requête de récupération des éléments via Ajax.
                 */
                'ajax'      => [],
                /**
                 * @var array $args Arguments complémentaires porté par la requête Ajax.
                 */
                'args'      => [],
                /**
                 * @var array $button Liste des attributs de configuration du bouton d'ajout d'un élément.
                 */
                'button'    => [],
                /**
                 *
                 */
                'classes'   => [],
                /**
                 * @var int $max Nombre maximum de valeur pouvant être ajoutées. -1 par défaut, pas de limite.
                 */
                'max'       => -1,
                /**
                 * @var bool $removable Activation du déclencheur de suppression des éléments.
                 */
                'removable' => true,
                /**
                 * @var bool|array $sortable Activation de l'ordonnacemment des éléments|Liste des attributs de configuration.
                 * @see http://api.jqueryui.com/sortable/
                 */
                'sortable'  => true,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $defaultClasses = [
            'addnew'  => 'FieldRepeater-addnew ThemeButton--primary ThemeButton--normal',
            'content' => 'FieldRepeater-itemContent',
            'down'    => 'FieldRepeater-itemSortDown ThemeFeed-itemSortDown',
            'item'    => 'FieldRepeater-item ThemeFeed-item',
            'items'   => 'FieldRepeater-items ThemeFeed-items',
            'order'   => 'FieldRepeater-itemOrder ThemeFeed-itemOrder',
            'remove'  => 'FieldRepeater-itemRemove ThemeFeed-itemRemove',
            'sort'    => 'FieldRepeater-itemSortHandle ThemeFeed-itemSortHandle',
            'up'      => 'FieldRepeater-itemSortUp ThemeFeed-itemSortUp',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set(["classes.{$k}" => sprintf($this->get("classes.{$k}", '%s'), $v)]);
        }

        $this->set(
            [
                'attrs.class'        => trim(sprintf($this->get('attrs.class', '%s'), ' FieldRepeater')),
                'attrs.data-id'      => $this->getId(),
                'attrs.data-control' => $this->get('attrs.data-control', 'repeater'),
            ]
        );

        $button = $this->get('button');
        $button = is_string($button) ? ['content' => $button] : $button;
        $button = array_merge(
            [
                'tag'     => 'a',
                'content' => __('Ajouter un élément', 'tify'),
            ],
            $button
        );
        $this->set('button', $button);

        if (($this->get('button.tag') === 'a') && !$this->get('button.attrs.href')) {
            $this->set('button.attrs.href', "#{$this->get('attrs.id')}");
        }
        $this->set('button.attrs.data-control', 'repeater.addnew');

        if ($sortable = $this->get('sortable')) {
            if (!is_array($sortable)) {
                $sortable = [];
            }
            $this->set(
                'sortable',
                array_merge(
                    [
                        'placeholder' => 'FieldRepeater-itemPlaceholder',
                        'axis'        => 'y',
                    ],
                    $sortable
                )
            );
        }

        $this->set(
            'attrs.data-options',
            [
                'ajax'      => array_merge(
                    [
                        'url'      => $this->getXhrUrl(),
                        'data'     => [
                            'viewer' => $this->get('viewer'),
                            'args'   => $this->get('args', []),
                            'max'    => $this->get('max'),
                            'name'   => $this->getName(),
                        ],
                        'dataType' => 'json',
                        'method'   => 'post',
                    ],
                    $this->get('ajax', [])
                ),
                'classes'   => $this->get('classes', []),
                'removable' => $this->get('removable'),
                'sortable'  => $this->get('sortable'),
            ]
        );

        $this->set('value', array_values(Arr::wrap($this->get('value', []))));

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->fieldManager()->resources('/views/repeater');
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        $max = Request::input('max', -1);
        $index = Request::input('index', 0);

        $this->set(
            [
                'name'   => Request::input('name', ''),
                'viewer' => Request::input('viewer', []),
            ]
        )->parse();

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre de valeur maximum atteinte', 'tify'),
            ];
        } else {
            return [
                'success' => true,
                'data'    => $this->view('item-wrap', Request::all()),
            ];
        }
    }
}