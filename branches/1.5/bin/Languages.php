<?php
namespace tiFy;

use tiFy\App;

class Languages extends App
{
    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        // Définition des événements
        $this->appAddAction('plugins_loaded');
    }

    /**
     * EVENEMENTS
     */
    /**
     * Après le chargement des plugins
     * 
     * @return void
     */
    public function plugins_loaded()
    {
        // Chargement des traductions
        load_muplugin_textdomain(
            'tify',
            '/presstify/languages/'
        );
    }
}