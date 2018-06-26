<?php

namespace tiFy\AdminView;

use tiFy\AdminView\AdminViewMenuController;
use tiFy\Kernel\Layout\AbstractLayoutViewController;

class AdminViewBaseController extends AbstractLayoutViewController implements AdminViewControllerInterface
{
    /**
     * Ecran courant d'affichage de la page.
     * @var null|\WP_Screen
     */
    protected $screen;

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('admin_menu');
    }

    /**
     * Initialisation du menu d'administration Wordpress.
     *
     * @return void
     */
    public function admin_menu()
    {
        new AdminViewMenuController($this->layout());
    }

    /**
     * {@inheritdoc}
     */
    public function getHookname()
    {
        return $this->layout()->get('hookname');
    }

    /**
     * {@inheritdoc}
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * Affichage de l'écran courant.
     * @todo
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page courante de l'interface d'administration de Wordpress.
     *
     * @return void
     */
    public function current_screen($wp_screen)
    {
        if ($wp_screen->id !== $this->getHookname()) :
            return;
        endif;

        $this->screen = $wp_screen;

        if (method_exists($this, 'admin_enqueue_scripts')) :
            $this->appAddAction('admin_enqueue_scripts');
        endif;

        $this->appAddAction('admin_notices');
    }

    /**
     * {@inheritdoc}
     */
    public function isAdmin()
    {
        return true;
    }
}