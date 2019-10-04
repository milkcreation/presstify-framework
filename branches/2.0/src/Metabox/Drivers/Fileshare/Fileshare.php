<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\Fileshare;

use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\{Request, Router};

class Fileshare extends MetaboxDriver
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
                $items[] = $this->item($index, (int)$value);
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
            'filetype'  => '', // video || application/pdf || video/flv, video/mp4,
            'max'       => -1,
            'removable' => true,
            'sortable'  => true,
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'fileshare',
            'title' => __('Partage de fichier', 'tify'),
        ]);
    }

    /**
     * Récupération de l'url de traitement Xhr.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Définition de l'url de traitement Xhr.
     *
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url = null): self
    {
        $this->url = is_null($url)
            ? Router::xhr(md5('MetaboxFileshare--' . static::$instance), [$this, 'xhrResponse'])->getUrl()
            : $url;

        return $this;
    }

    /**
     * Définition d'un élément.
     *
     * @param int|string $index Indice de l'élément.
     * @param int|null $value Identifiant de qualification du média.
     *
     * @return array
     */
    public function item($index, ?int $value = null): array
    {
        $name = $this->get('name');
        $index = !is_numeric($index) ? $index : uniqid();

        return [
            'name'  => $this->get('params.max', -1) === 1 ? "{$name}[]" : "{$name}[{$index}]",
            'value' => $value,
            'index' => $index,
            'icon'  => wp_get_attachment_image($value, [48, 64], true),
            'title' => get_the_title($value),
            'mime'  => get_post_mime_type($value),
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(): MetaboxDriverContract
    {
        parent::parse();

        $this->params([
            'attrs.class'        => sprintf($this->get('attrs.class', '%s'), 'MetaboxFileshare'),
            'attrs.data-control' => 'metabox-fileshare',
        ]);

        if ($sortable = $this->get('sortable')) {
            $this->params([
                'sortable' => array_merge([
                    'placeholder' => 'MetaboxFileshare-itemPlaceholder',
                    'axis'        => 'y',
                ], is_array($sortable) ? $sortable : []),
            ]);
        }

        $this->params([
            'attrs.data-options' => [
                'ajax'      => array_merge([
                    'data'   => [
                        'max'    => $this->params('max', -1),
                        'name'   => $this->get('name'),
                        'viewer' => $this->get('viewer', []),
                    ],
                    'method' => 'post',
                    'url'    => $this->getUrl(),
                ]),
                'media'   => [
                    'multiple' => ($this->params('max', -1) === 1 ? false : true),
                    'library' => [
                        'type' => $this->params('filetype'),
                    ],
                ],
                'removable' => $this->params('removable'),
                'sortable'  => $this->params('sortable'),
            ],
        ]);

        return $this;
    }

    /**
     * Récupération des champs via Ajax.
     *
     * @return array
     */
    public function xhrResponse(): array
    {
        $index = Request::input('index');
        $max = Request::input('max', 0);
        $value = (int)Request::input('value');

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre maximum de fichiers partagés atteint.', 'tify'),
            ];
        } else {
            $this->set([
                'name'   => Request::input('name', []),
                'params' => [
                    'max' => $max,
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