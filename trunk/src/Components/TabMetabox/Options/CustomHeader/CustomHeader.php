<?php

namespace tiFy\Components\TabMetabox\Options\CustomHeader;

use tiFy\Field\Field;
use tiFy\TabMetabox\Controller\AbstractTabContentOptionsController;

class CustomHeader extends AbstractTabContentOptionsController
{
    private $option_name = 'custom_header';

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        register_setting($this->object_name, $this->option_name);
    }

    /**
     * Chargement de la page d'administration courante de Wordpress.
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    public function load($wp_screen)
    {
        $this->appAddAction('admin_enqueue_scripts', function(){
            $this->appServiceGet(Field::class)->enqueue('MediaImage');
        });
    }

    /**
     * Affichage.
     *
     * @param \WP_Post $post Objet post Wordpress.
     * @param array $args Liste des variables passés en argument.
     *
     * @return string
     */
    public function display($args)
    {
        return Field::MediaImage(
            array_merge(
                [
                    'media_library_title'  => __('Personnalisation de l\'image d\'entête', 'tify'),
                    'media_library_button' => __('Utiliser comme image d\'entête', 'tify'),
                    'name'                 => $this->option_name,
                    'value'                => get_option($this->option_name)
                ],
                $args
            )
        );
    }
}