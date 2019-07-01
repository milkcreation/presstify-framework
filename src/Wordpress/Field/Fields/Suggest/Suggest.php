<?php declare(strict_types=1);

namespace tiFy\Wordpress\Field\Fields\Suggest;

use tiFy\Field\Fields\Suggest\Suggest as BaseSuggest;
use tiFy\Support\Proxy\Request as req;
use tiFy\Wordpress\{Contracts\Field\Suggest as SuggestContract,
    Query\QueryPost,
    Query\QueryPosts,
    Query\QueryTerm,
    Query\QueryTerms};

class Suggest extends BaseSuggest implements SuggestContract
{
    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        switch ('post') {
            case 'post' :
            default :
                return $this->xhrResponsePostQuery(...$args);
                break;
            case 'term' :
                return $this->xhrResponseTermQuery(...$args);
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function xhrResponsePostQuery(...$args): array
    {
        $args = array_merge([
            'post_type' => 'any'
        ], req::input('query_args', []));

        $posts = QueryPosts::createFromArgs($args) ?: [];

        $items = collect($posts)->map(function (QueryPost &$item) {
            return [
                'label'  => (string)$item->getTitle(),
                'value'  => (string)$item->getId(),
                'render' => (string)$item->getTitle(),
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
    public function xhrResponseTermQuery(...$args): array
    {
        $terms = QueryTerms::createFromArgs(req::input('query_args', [])) ?: [];

        $items = collect($terms)->map(function (QueryTerm &$item) {
            return [
                'label'  => (string)$item->getName(),
                'value'  => (string)$item->getId(),
                'render' => (string)$item->getName(),
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