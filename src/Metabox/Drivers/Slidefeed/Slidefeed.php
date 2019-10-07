<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\Slidefeed;

use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\{Request, Router};

class Slidefeed extends MetaboxDriver
{
    /**
     * Indice de l'intance courante.
     * @var integer
     */
    static $instance = 0;

    /**
     * Url de traitement de requêtes XHR.
     * @var string
     */
    protected $url = '';

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        static::$instance++;
        $this->setUrl();
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        if ($values = $this->value()) {
            $items = [];
            array_walk($values, function ($value, $index) use (&$items) {
                $items[] = $this->item($index, $value);
            });
            $this->set('items', $items);
        }

        return parent::content();
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            'addnew'  => true,
            'fields'  => ['image', 'title', 'url', 'caption'],
            'max'     => -1,
            'suggest' => true,
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'slidefeed',
            'title' => __('Diaporama', 'tify'),
        ]);
    }

    /**
     * Récupération de l'url de traitement XHR.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Définition de l'url de traitement XHR.
     *
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url = null): self
    {
        $this->url = is_null($url)
            ? Router::xhr(md5('MetaboxSlidefeed--' . static::$instance), [$this, 'xhrResponse'])->getUrl()
            : $url;

        return $this;
    }

    /**
     * Définition d'un élément.
     *
     * @param int|string $index Indice de l'élément.
     * @param array $value Données.
     *
     * @return array
     */
    public function item($index, $value): array
    {
        $name = $this->get('name');
        $index = !is_numeric($index) ? $index : uniqid();

        return [
            'fields' => $this->get('params.fields', []),
            'index'  => $index,
            'name'   => $this->get('params.max', -1) === 1 ? "{$name}[]" : "{$name}[{$index}]",
            'value'  => $value,
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(): MetaboxDriverContract
    {
        parent::parse();

        $defaultClasses = [
            'addnew'  => 'MetaboxSlidefeed-addnew ThemeButton--primary ThemeButton--normal',
            'down'    => 'MetaboxSlidefeed-itemSortDown ThemeFeed-itemSortDown',
            'item'    => 'MetaboxSlidefeed-item ThemeFeed-item',
            'items'   => 'MetaboxSlidefeed-items ThemeFeed-items',
            'order'   => 'MetaboxSlidefeed-itemSortOrder ThemeFeed-itemOrder',
            'remove'  => 'MetaboxSlidefeed-itemRemove ThemeFeed-itemRemove',
            'sort'    => 'MetaboxSlidefeed-itemSortHandle ThemeFeed-itemSortHandle',
            'suggest' => 'MetaboxSlidefeed-suggest',
            'up'      => 'MetaboxSlidefeed-itemSortUp ThemeFeed-itemSortUp',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set("classes.{$k}", sprintf($this->get("classes.{$k}", '%s'), $v));
        }

        $this->params([
            'addnew'             => [
                'attrs'   => [
                    'data-control' => 'metabox-slidefeed.addnew',
                ],
                'content' => __('Vignette personnalisée', 'tify'),
            ],
            'attrs.class'        => sprintf($this->get('attrs.class', '%s'), 'MetaboxSlidefeed'),
            'attrs.data-control' => 'metabox-slidefeed',
            'attrs.data-options' => [
                'ajax'    => [
                    'data'     => [
                        'fields' => $this->params('fields', []),
                        'max'    => $this->params('max', -1),
                        'name'   => $this->get('name'),
                        'viewer' => $this->get('viewer', []),
                    ],
                    'dataType' => 'json',
                    'method'   => 'post',
                    'url'      => $this->getUrl(),
                ],
                'classes' => $this->get('classes', []),
            ],
            'options'            => array_merge([
                'ratio'       => '16:9',
                'size'        => 'full',
                'nav'         => true,
                'tab'         => true,
                'progressbar' => false,
            ], $exists['options'] ?? []),
            'suggest'            => [
                'ajax'    => true,
                'attrs'   => [
                    'data-control' => 'metabox-slidefeed.suggest',
                ],
                'classes' => [
                    'wrap' => '%s MetaboxSlidefeed-suggestWrap',
                ],
            ],
        ]);

        return $this;
    }

    /**
     * Récupération d'un élément via Ajax
     *
     * @param array ...$args Liste des arguments de requête passés dans l'url.
     *
     * @return array
     */
    public function xhrResponse(...$args): array
    {
        $index = Request::input('index');
        $max = Request::input('max', 0);
        $value = Request::input('value', '');

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre maximum de vignette atteint.', 'tify'),
            ];
        } else {
            $this->set([
                'name'   => Request::input('name', []),
                'params' => [
                    'fields' => Request::input('fields', []),
                    'max'    => $max,
                ],
                'viewer' => Request::input('viewer', []),
            ]);

            return [
                'success' => true,
                'data'    => (string)$this->viewer('item-wrap', $this->item($index, $value)),
            ];
        }
    }
}