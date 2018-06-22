<?php

namespace tiFy\AdminView\AdminMenu;

use tiFy\AdminView\Interop\AbstractAttributesAwareController;

class AdminMenuController extends AbstractAttributesAwareController implements AdminMenuInterface
{
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