<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxDisplayUserInterface;

abstract class AbstractMetaboxDisplayUserController
    extends AbstractMetaboxDisplayController
    implements MetaboxDisplayUserInterface
{
    /**
     * {@inheritdoc}
     */
    public function display($user, $args = [])
    {
        return $this->viewer('display', $this->all());
    }
}