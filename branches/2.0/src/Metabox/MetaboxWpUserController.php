<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxWpUserController as MetaboxWpUserControllerContract;

abstract class MetaboxWpUserController extends MetaboxController implements MetaboxWpUserControllerContract
{
    /**
     * {@inheritdoc}
     */
    public function content($user = null, $args = null, $null = null)
    {
        return parent::content($user, $args, $null);
    }

    /**
     * {@inheritdoc}
     */
    public function header($user = null, $args = null, $null = null)
    {
        return parent::header($user, $args, $null);
    }
}