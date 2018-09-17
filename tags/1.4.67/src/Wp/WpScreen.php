<?php

namespace tiFy\Wp;

use tiFy\Contracts\Wp\WpScreenInterface;
use \WP_Screen;

class WpScreen implements WpScreenInterface
{
    /**
     * Instance de l'écran en relation.
     * @var WP_Screen
     */
    protected $screen;

    /**
     * Nom de qualification de l'objet Wordpress associé.
     * @var string
     */
    protected $objectName = '';

    /**
     * Typel'objet Wordpress associé.
     * @var string
     */
    protected $objectType = '';

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(WP_Screen $wp_screen)
    {
        $this->screen = $wp_screen;

        $this->parse();
    }

    /**
     * {@inheritdoc}
     */
    public static function get($screen)
    {
        if ($screen instanceof WP_Screen) :
            return new static($screen);
        elseif(is_string($screen)):
            if (preg_match('#(.*)@(options|post_type|taxonomy|user)#', $screen, $matches)) :
                switch($matches[2]) :
                    case 'post_type' :
                        $screen = WP_Screen::get($matches[1]);
                        break;
                    case 'taxonomy' :
                        $screen = WP_Screen::get('edit-' . $matches[1]);
                        break;
                    case 'options' :
                        $screen = WP_Screen::get('settings_page_' . $matches[1]);
                        break;
                endswitch;
            else :
                $screen = WP_Screen::get($screen);
            endif;

            if ($screen instanceof WP_Screen) :
                return new static($screen);
            endif;
        else :
            return null;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getHookname()
    {
        return $this->screen->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrent()
    {
        return (($current_screen = get_current_screen()) && ($current_screen->id === $this->getHookname()));
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        if(preg_match('#^settings_page_(.*)#', $this->screen->id, $matches)) :
            $this->objectName = $matches[1];
            $this->objectType = 'options';
        elseif(preg_match('#^edit-(.*)#', $this->screen->id, $matches) && taxonomy_exists($matches[1])) :
            $this->objectName = $matches[1];
            $this->objectType = 'taxonomy';
        elseif(post_type_exists($this->screen->id)) :
            $this->objectName = $this->screen->id;
            $this->objectType = 'post_type';
        endif;
    }
}