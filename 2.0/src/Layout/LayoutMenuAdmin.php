<?php

namespace tiFy\Layout;

use tiFy\Contracts\Layout\LayoutFactoryAdminInterface;
use tiFy\Contracts\Layout\LayoutMenuAdminInterface;
use tiFy\Kernel\Parameters\AbstractParametersBag;

class LayoutMenuAdmin extends AbstractParametersBag implements LayoutMenuAdminInterface
{
    /**
     * Instance de la fabrique de disposition associée.
     * @var LayoutFactoryAdminInterface
     */
    protected $factory;

    /**
     * CONSTRUCTEUR.
     *
     * @param LayoutFactoryAdminInterface $factory Instance de la fabrique de disposition associée.
     *
     * @return void
     */
    public function __construct(LayoutFactoryAdminInterface $factory)
    {
        $this->factory = $factory;

        parent::__construct($this->factory->get('admin_menu', []));
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'menu_slug'   => $this->factory->getName(),
            'parent_slug' => '',
            'page_title'  => $this->factory->getName(),
            'menu_title'  => $this->factory->getName(),
            'capability'  => 'manage_options',
            'icon_url'    => null,
            'position'    => null,
            'function'    => function() {
                echo $this->factory->layout();
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
            $hookname = add_menu_page(
                $this->get('page_title'),
                $this->get('menu_title'),
                $this->get('capability'),
                $this->get('menu_slug'),
                $this->get('function'),
                $this->get('icon_url'),
                $this->get('position')
            );
        else :
            $hookname = add_submenu_page(
                $this->get('parent_slug'),
                $this->get('page_title'),
                $this->get('menu_title'),
                $this->get('capability'),
                $this->get('menu_slug'),
                $this->get('function')
            );
        endif;

        $this->factory->set('hookname', $hookname);

        $this->factory->set('page_url', menu_page_url($this->get('menu_slug'), false));
    }
}