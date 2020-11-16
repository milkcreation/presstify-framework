<?php declare(strict_types=1);

namespace tiFy\Wordpress\Field\Driver\Suggest;

use Illuminate\Support\Collection;
use tiFy\Contracts\Field\FieldDriver as FieldDriverContract;
use tiFy\Field\Driver\Suggest\Suggest as BaseSuggest;
use tiFy\Support\Proxy\Request;
use tiFy\Wordpress\Contracts\Field\Suggest as SuggestContract;
use tiFy\Wordpress\Query\QueryPost;
use tiFy\Wordpress\Query\QueryTerm;
use tiFy\Wordpress\Query\QueryUser;
use WP_Query;

class Suggest extends BaseSuggest implements SuggestContract
{
    /**
     * @inheritDoc
     */
    public function parse(): FieldDriverContract
    {
        if ($this->get('wp_query', 'post') || $this->get('ajax')) {
            if (!$this->get('ajax')) {
                $this->set('ajax', []);
            }
            $this->set('ajax.data.wp_query', $this->get('wp_query', 'post'));
        }

        parent::parse();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        switch (Request::input('wp_query')) {
            case 'post' :
            default :
                $response = $this->xhrResponsePostQuery(...$args);
                break;
            case 'term' :
                $response = $this->xhrResponseTermQuery(...$args);
                break;
            case 'user' :
                $response = $this->xhrResponseUserQuery(...$args);
                break;
            case 'custom':
                $response = parent::xhrResponse(...$args);
                break;
        }

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponsePostQuery(...$args): array
    {
        $term = Request::input('_term', '');
        $paged = Request::input('_paged', 1);
        $per_page = get_option('posts_per_page');

        $wpQuery = new WP_Query();

        $query_args = array_merge(['post_type' => 'any'], Request::input('query_args', []), [
            's'              => $term,
            'paged'          => $paged,
            'posts_per_page' => $per_page,
        ]);

        if ($ids = $wpQuery->query(array_merge($args, ['s' => $term]))) {
            $posts = QueryPost::fetchFromIds($query_args);
            $count = count($posts);
            $found = $wpQuery->found_posts;

            $items = (new Collection($posts))->map(function (QueryPost $item) {
                return [
                    'alt'   => (string)$item->getId(),
                    'label' => (string)$item->getTitle(),
                    'value' => (string)$item->getTitle(true),
                ];
            })->all();

            $more = (($count >= $per_page) && ($found > ($paged * $count))) ? [
                'data'  => [
                    '_paged' => ++$paged,
                ],
                'count' => $count,
                'found' => $found,
                'loader' => true,
                'html'   => '+'
            ] : null;


            return [
                'success' => true,
                'data'    => compact('items', 'more'),
            ];
        } else {
            return ['success' => false];
        }
    }

    /**
     * @inheritDoc
     */
    public function xhrResponseTermQuery(...$args): array
    {
        $query_args = array_merge(Request::input('query_args', []), ['search' => Request::input('_term', '')]);

        $terms = QueryTerm::fetchFromArgs($query_args);

        $items = (new Collection($terms))->map(function (QueryTerm $item) {
            return [
                'alt'   => (string)$item->getId(),
                'label' => (string)$item->getName(),
                'value' => (string)$item->getName(),
            ];
        })->all();

        return [
            'success' => true,
            'data'    => [
                'items' => $items,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function xhrResponseUserQuery(...$args): array
    {
        $query_args = array_merge(Request::input('query_args', []), [
            'search' => ($term = Request::input('_term', '')) ? '*' . trim($term, '*') . '*' : '',
        ]);

        $users = QueryUser::fetchFromArgs($query_args);

        $items = (new Collection($users))->map(function (QueryUser $item) {
            return [
                'alt'   => (string)$item->getId(),
                'label' => (string)$item->getDisplayName(),
                'value' => (string)$item->getDisplayName(),
            ];
        })->all();

        return [
            'success' => true,
            'data'    => [
                'items' => $items,
            ],
        ];
    }
}