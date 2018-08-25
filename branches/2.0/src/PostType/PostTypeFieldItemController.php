<?php

namespace tiFy\PostType;

use tiFy\App\Partial\AbstractAppPartial;
use tiFy\App\AppInterface;

class PostTypeFieldItemController extends AbstractAppPartial
{
    /**
     * Classe de rappel du controleur de l'application associée.
     * @var \tiFy\PostType\PostTypeItemController
     */
    protected $app;

    /**
     * Liste des contextes d'accroche autorisés.
     * @var array
     */
    protected $hooks = [
        'edit_form_top',
        'edit_form_before_permalink',
        'edit_form_after_title',
        'edit_form_after_editor',
        'submitpage_box',
        'submitpost_box',
        'edit_page_form',
        'edit_form_advanced',
        'dbx_post_sidebar'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param AppInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], AppInterface $app)
    {
        parent::__construct($attrs, $app);

        $this->app->appAddAction('current_screen', [$this, '_load']);
    }

    /**
     *
     */
    public function load($wp_screen)
    {
        if ($wp_screen->id === $this->getPostType()) :
            switch($this->getContext()) :
                case 'edit_form_top' :

                    break;
                case 'edit_form_before_permalink':
                    break;
                case 'edit_form_after_title':
                    break;
                case 'edit_form_after_editor':
                    break;
                case 'submitpage_box':
                    break;
                case 'submitpost_box':
                    break;
                case 'edit_page_form':
                    break;
                case 'edit_form_advanced':
                    break;
                case 'dbx_post_sidebar':
                    break;
            endswitch;
        endif;
    }

    /**
     *
     */
    public function getContext()
    {
        return $this->get('context');
    }

    /**
     *
     */
    public function getPostType()
    {
        return $this->app->getName();
    }

    /**
     *
     */
    public function display()
    {

    }
}