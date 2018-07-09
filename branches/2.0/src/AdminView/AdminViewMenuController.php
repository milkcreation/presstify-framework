<?php

namespace tiFy\AdminView;

use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\Item\AbstractAppItemController;
use tiFy\Apps\Layout\LayoutControllerInterface;

class AdminViewMenuController extends AbstractAppItemController
{
    /**
     * Classe de rappel du controleur de l'interface associée.
     * @var LayoutControllerInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(LayoutControllerInterface $app)
    {
        parent::__construct($app->get('admin_menu', []), $app);
    }

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
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('parent_slug')) :
            $hookname = \add_menu_page(
                $this->get('page_title'),
                $this->get('menu_title'),
                $this->get('capability'),
                $this->get('menu_slug'),
                $this->get('function'),
                $this->get('icon_url'),
                $this->get('position')
            );
        else :
            $hookname = \add_submenu_page(
                $this->get('parent_slug'),
                $this->get('page_title'),
                $this->get('menu_title'),
                $this->get('capability'),
                $this->get('menu_slug'),
                $this->get('function')
            );
        endif;

        $this->app->set('hookname', $hookname);
        $this->app->set('page_url', \menu_page_url($this->get('menu_slug'), false));
    }
}