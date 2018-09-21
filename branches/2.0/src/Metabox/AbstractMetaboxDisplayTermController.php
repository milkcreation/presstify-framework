<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxDisplayTermInterface;

abstract class AbstractMetaboxDisplayTermController
    extends AbstractMetaboxDisplayController
    implements MetaboxDisplayTermInterface
{
    /**
     * {@inheritdoc}
     */
    public function display($term, $taxonomy, $args = [])
    {
        return $this->viewer('display', $this->all());
    }
}