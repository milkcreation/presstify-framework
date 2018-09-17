<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxContentUserInterface;

abstract class AbstractMetaboxContentUserController
    extends AbstractMetaboxContentController
    implements MetaboxContentUserInterface
{
    /**
     * {@inheritdoc}
     */
    public function display($user, $args = [])
    {
        return $this->viewer('display', $this->all());
    }
}