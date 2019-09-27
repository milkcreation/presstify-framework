<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\RelatedPost;

use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\{Request, Router};
use tiFy\Wordpress\Query\QueryPost;

class RelatedPost extends MetaboxDriver
{
    /**
     * Indice de l'intance courante.
     * @var integer
     */
    static $instance = 0;

    /**
     * Liste des éléments.
     * @var array
     */
    protected $items = [];

    /**
     * Ordre des éléments.
     * @var int
     */
    protected $order = 0;

    /**
     * Url de traitement Xhr.
     * @var string Url de traitement
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
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'   => 'related_post',
            'params' => [
                'elements'    => ['title', 'ico'],
                'max'         => -1,
                'placeholder' => __('Rechercher un contenu en relation', 'tify'),
                'post_type'   => 'any',
                'post_status' => 'publish',
                'query_args'  => [],
            ],
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
                'id'           => 'MetaboxRelatedPost--' . static::$instance,
                'class'        => 'MetaboxRelatedPost MetaboxRelatedPost--' . $this->name(),
                'data-control' => 'metabox.related-post',
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
                    'class'       => 'MetaboxRelatedPost-suggestInput',
                    'placeholder' => $this->params('placeholder'),
                ],
            ],
        ]);

        $this->params([
            'attrs.data-options' => [
                'ajax'     => [
                    'data'     => [
                        'name' => $this->name(),
                    ],
                    'dataType' => 'json',
                    'method'   => 'post',
                    'url'      => $this->getUrl(),
                ],
                'sortable' => $this->params('sortable', []),
            ],
        ]);

        return $this;
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
            ? Router::xhr(md5('MetaboxRelatedPost--' . static::$instance), [$this, 'xhrResponse'])->getUrl()
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
        if ($item = QueryPost::createFromId(Request::input('post_id'))) {
            return [
                'success' => true,
                'data'    => (string)$this->viewer('item', [
                    'index' => Request::input('index', 0),
                    'item'  => $item,
                    'name'  => Request::input('name', ''),
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