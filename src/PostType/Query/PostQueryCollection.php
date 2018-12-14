<?php

namespace tiFy\PostType\Query;

use tiFy\Contracts\PostType\PostQueryItem;
use tiFy\Contracts\PostType\PostQueryCollection as PostQueryCollectionContract;
use tiFy\Kernel\Collection\QueryCollection;

class PostQueryCollection extends QueryCollection implements PostQueryCollectionContract
{
    /**
     * Liste des éléments déclarés.
     * @var PostQueryItem[] $items
     */
    protected $items = [];

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        return $this->collect()->pluck('ID')->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getTitles()
    {
        return $this->collect()->pluck('post_title')->all();
    }
}