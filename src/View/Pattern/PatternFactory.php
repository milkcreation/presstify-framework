<?php

namespace tiFy\View\Pattern;

use tiFy\Contracts\View\PatternController;
use tiFy\Contracts\View\PatternFactory as PatternFactoryContract;
use tiFy\Kernel\Params\ParamsBag;

class PatternFactory extends ParamsBag implements PatternFactoryContract
{
    /**
     * Nom de qualification de la disposition associée.
     * @var string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification de la disposition associée.
     * @param array $attrs Attributs de configuration de la disposition associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);

        add_action(
            'admin_menu',
            function () {
                if ($attrs = $this->get('admin_menu', [])) :
                    $menu = params(
                        array_merge(
                            [
                                'menu_slug'   => $this->getName(),
                                'parent_slug' => '',
                                'page_title'  => $this->getName(),
                                'menu_title'  => $this->getName(),
                                'capability'  => 'manage_options',
                                'icon_url'    => null,
                                'position'    => null,
                                'function'    => function() {
                                    echo call_user_func_array($this->getContent(), []);
                                }
                            ],
                            $attrs
                        )
                    );

                    $hookname = !$menu->get('parent_slug')
                        ? add_menu_page(
                            $menu->get('page_title'),
                            $menu->get('menu_title'),
                            $menu->get('capability'),
                            $menu->get('menu_slug'),
                            $menu->get('function'),
                            $menu->get('icon_url'),
                            $menu->get('position')
                        )
                        : add_submenu_page(
                            $menu->get('parent_slug'),
                            $menu->get('page_title'),
                            $menu->get('menu_title'),
                            $menu->get('capability'),
                            $menu->get('menu_slug'),
                            $menu->get('function')
                        );

                    $this->set('hookname', $hookname);
                    $this->set('page_url', menu_page_url($this->get('menu_slug'), false));

                    add_action(
                        'current_screen',
                        function (\WP_Screen $wp_screen) {
                            if ($wp_screen->id === $this->get('hookname')) :
                                $this->set('_wp_screen',  $wp_screen);

                                $wp_screen->add_option(
                                    'per_page',
                                    [
                                        'option' => $this->get('per_page_option_name')
                                    ]
                                );

                                if ($this->getContent() instanceof PatternController) :
                                    $this->getContent()->load();
                                endif;
                            endif;
                        }
                    );

                endif;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->get('content');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $content = $this->get('content');
        if (is_string($content) && class_exists($content)) :
            $this->set('content', new $content($this));
        endif;
    }
}