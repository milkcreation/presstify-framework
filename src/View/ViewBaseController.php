<?php

namespace tiFy\AdminView;

use tiFy\Apps\Layout\AbstractLayoutViewController;

class ViewBaseController extends AbstractLayoutViewController implements ViewControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAdmin()
    {
        return false;
    }
}