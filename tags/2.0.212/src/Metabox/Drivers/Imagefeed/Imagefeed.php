<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\Imagefeed;

use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Metabox\MetaboxDriver;
use tiFy\Support\{Img, Proxy\Request, Proxy\Router};
use tiFy\Validation\Validator;

class Imagefeed extends MetaboxDriver
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
            'max' => -1,
            'library'   => true,
            'removable' => true,
            'sortable'  => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'imagefeed',
            'title' => __('Images', 'tify'),
        ]);
    }

    /**
     * Définition d'un élément.
     *
     * @param int|string $index Indice de l'élément.
     * @param string|int $value Identifiant de qualification du média.
     *
     * @return array
     */
    public function item($index, $value): array
    {
        $name = $this->get('name');
        $index = !is_numeric($index) ? $index : uniqid();

        $src = '';
        if (is_numeric($value)) {
            if ($img = wp_get_attachment_image_src($value, 'thumbnail')) {
                $src = $img[0];
            }
        } elseif (is_string($value)) {
            if (Validator::url()->validate($value)) {
                $src = $value;
            } elseif (file_exists($value)) {
              $src= Img::getBase64Src($value);
            }
        }

        return [
            'index' => $index,
            'name'  => $this->get('params.max', -1) === 1 ? "{$name}[]" : "{$name}[{$index}]",
            'value' => $value,
            'src'   => $src
        ];
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
     * @inheritDoc
     */
    public function parse(): MetaboxDriverContract
    {
        parent::parse();

        $this->params([
            'attrs.class'        => sprintf($this->get('attrs.class', '%s'), 'MetaboxImagefeed'),
            'attrs.data-control' => 'metabox-imagefeed',
        ]);

        if ($sortable = $this->get('sortable')) {
            $this->params([
                'sortable' => array_merge([
                    'placeholder' => 'MetaboxImagefeed-itemPlaceholder',
                    'axis'        => 'y',
                ], is_array($sortable) ? $sortable : []),
            ]);
        }

        $this->params([
            'attrs.data-options' => [
                'ajax'      => [
                    'data'   => [
                        'max'    => $this->params('max', -1),
                        'name'   => $this->get('name'),
                        'viewer' => $this->get('viewer', []),
                    ],
                    'dataType' => 'json',
                    'method' => 'post',
                    'url'    => $this->getUrl(),
                ],
                'library'   => $this->params('library'),
                'removable' => $this->params('removable'),
                'sortable'  => $this->params('sortable'),
            ],
        ]);

        return $this;
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
            ? Router::xhr(md5('MetaboxImagefeed--' . static::$instance), [$this, 'xhrResponse'])->getUrl()
            : $url;

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
                'data'    => __('Nombre maximum d\'images atteint.', 'tify'),
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