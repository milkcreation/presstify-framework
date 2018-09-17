<?php

namespace tiFy\AdminView;

use tiFy\Contracts\App\AppInterface;
use tiFy\App\Item\AbstractAppItemController;
use tiFy\App\Layout\LayoutInterface;

class AdminViewMenuController extends AbstractAppItemController
{
    /**
     * Classe de rappel du controleur de l'interface associée.
     * @var LayoutInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(LayoutInterface $app)
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
            'function'    => function() {
                echo call_user_func([$this->app, 'render']);
            }
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