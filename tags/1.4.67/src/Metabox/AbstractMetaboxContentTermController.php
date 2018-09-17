<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxContentTermInterface;

abstract class AbstractMetaboxContentTermController
    extends AbstractMetaboxContentController
    implements MetaboxContentTermInterface
{
    /**
     * {@inheritdoc}
     */
    public function display($term, $taxonomy, $args = [])
    {
        return $this->viewer('display', $this->all());
    }
}