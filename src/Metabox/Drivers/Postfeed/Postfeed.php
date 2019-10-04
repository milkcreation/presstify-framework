<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\Postfeed;

use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\{Request, Router};
use tiFy\Wordpress\Query\QueryPost;

class Postfeed extends MetaboxDriver
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
        $items = [];

        if (is_array($this->value())) {
            foreach ($this->value() as $id) {
                if ($item = QueryPost::createFromId($id)) {
                    $items[] = QueryPost::createFromId((int)$id);
                }
            }
        }
        $this->params(['items' => $items]);

        return parent::content();
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            'elements'    => ['title', 'ico'],
            'max'         => -1,
            'suggest'     => [],
            'placeholder' => __('Rechercher un contenu en relation', 'tify'),
            'query_args'  => [],
            'post_type'   => 'any',
            'post_status' => 'publish',
            'sortable'    => true,
            'removable'   => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'   => 'postfeed',
            'title'  => __('Éléments en relation', 'tify'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parse(): MetaboxDriverContract
    {
        parent::parse();

        $this->params([
            'attrs'   => [
                'data-control' => 'metabox.postfeed',
            ],
            'index'   => static::$instance,
            'suggest' => [
                'ajax'  => [
                    'data' => [
                        'query_args' => array_merge(['posts_per_page' => -1], $this->params('query_args', []), [
                            'post_type'   => $this->params('post_type', 'any'),
                            'post_status' => $this->params('post_status', 'publish'),
                        ]),
                    ],
                ],
                'alt'   => true,
                'attrs' => [
                    'placeholder' => $this->params('placeholder'),
                ]
            ],
        ]);

        $this->params([
            'attrs.data-options' => [
                'ajax'      => [
                    'data'   => [
                        'max'    => $this->params('max', -1),
                        'name'   => $this->get('name'),
                        'viewer' => $this->get('viewer', []),
                    ],
                    'dataType' => 'json',
                    'method'   => 'post',
                    'url'      => $this->getUrl(),
                ],
                'name'      => $this->name(),
                'removable' => $this->params('removable'),
                'sortable'  => $this->params('sortable'),
                'suggest'   => $this->params('suggest'),
                'viewer'
            ],
        ]);

        return $this;
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
            ? Router::xhr(md5('MetaboxPostfeed--' . static::$instance), [$this, 'xhrResponse'])->getUrl()
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

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre maximum de fichiers partagés atteint.', 'tify'),
            ];
        } elseif ($item = QueryPost::createFromId(Request::input('post_id'))) {
            $this->set([
                'name'   => Request::input('name', []),
                'params' => [
                    'max' => $max,
                ],
                'viewer' => Request::input('viewer', []),
            ]);

            return [
                'success' => true,
                'data'    => (string)$this->viewer('item-wrap', [
                    'item'  => $item
                ]),
            ];
        } else {
            return [
                'success' => false,
                'data'    => __('Impossible de récupérer le contenu associé', 'tify'),
            ];
        }
    }
}