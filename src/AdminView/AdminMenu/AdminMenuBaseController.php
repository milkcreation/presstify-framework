<?php

namespace tiFy\AdminView\AdminMenu;

use tiFy\AdminView\AdminViewControllerInterface;
use tiFy\Apps\Attributes\AbstractAttributesController;

class AdminMenuBaseController extends AbstractAttributesController implements AdminMenuInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
     * @var AdminViewControllerInterface
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'menu_slug'   => $this->app->getName(),
            'parent_slug' => '',
            'page_title'  => $this->app->getName(),
            'menu_title'  => $this->app->getName(),
            'capability'  => 'manage_options',
            'icon_url'    => null,
            'position'    => null,
            'function'    => [$this->app, 'render'],
        ];
    }

    /**
     * Initialisation du menu d'administration.
     *
     * @return void
     */
    public function admin_menu()
    {
        if (!$attrs = $this->all()) :
            return;
        endif;

        if (!$attrs['parent_slug']) :
            $hookname = \add_menu_page(
                $attrs['page_title'],
                $attrs['menu_title'],
                $attrs['capability'],
                $attrs['menu_slug'],
                $attrs['function'],
                $attrs['icon_url'],
                $attrs['position']
            );
        else :
            $hookname = \add_submenu_page(
                $attrs['parent_slug'],
                $attrs['page_title'],
                $attrs['menu_title'],
                $attrs['capability'],
                $attrs['menu_slug'],
                $attrs['function']
            );
        endif;

        $this->app->set('hookname', $hookname);
        $this->app->set('page_url', \menu_page_url($attrs['menu_slug'], false));
    }

}