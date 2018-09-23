<?php

namespace tiFy\View;

use tiFy\App\Layout\AbstractLayoutViewController;

class ViewBaseController extends AbstractLayoutViewController implements ViewControllerInterface
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('wp_loaded');
    }

    /**
     * A l'issue du chargement de Wordpress.
     *
     * @return void
     */
    public function wp_loaded()
    {
        $this->layout()->current();
    }

    /**
     * {@inheritdoc}
     */
    public function isAdmin()
    {
        return false;
    }
}