<?php

namespace tiFy\Languages;

use tiFy\Apps\AppController;

class Languages extends AppController
{
    /**
     * Initialiastion du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('plugins_loaded');
    }

    /**
     * Après le chargement des plugins.
     * 
     * @return void
     */
    public function plugins_loaded()
    {
        load_muplugin_textdomain('tify', '/presstify/languages/');
    }
}