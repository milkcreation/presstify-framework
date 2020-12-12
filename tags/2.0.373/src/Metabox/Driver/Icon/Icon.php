<?php declare(strict_types=1);

namespace tiFy\Metabox\Driver\Icon;

use tiFy\Contracts\Metabox\IconDriver as IconDriverContract;
use tiFy\Metabox\MetaboxDriver;

class Icon extends MetaboxDriver implements IconDriverContract
{
    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = 'icon';

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name'  => 'icon',
            'title' => __('Icône représentative', 'tify')
        ]);
    }
}