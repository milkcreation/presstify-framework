<?php declare(strict_types=1);

namespace tiFy\Metabox\Driver\Excerpt;

use tiFy\Contracts\Metabox\ExcerptDriver as ExcerptDriverContract;
use tiFy\Metabox\MetaboxDriver;

class Excerpt extends MetaboxDriver implements ExcerptDriverContract
{
    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = 'excerpt';

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'excerpt',
            'title' => __('Extrait', 'tify')
        ]);
    }
}