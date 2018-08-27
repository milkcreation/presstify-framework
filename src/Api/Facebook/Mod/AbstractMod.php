<?php

namespace tiFy\Api\Facebook\Mod;

use tiFy\App\AppController;

abstract class AbstractMod extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function __construct($callable = null)
    {
        parent::__construct();
        
        $this->appAddAction('tify_api_fb', (is_callable($callable) ? $callable : 'handler'), 10, 2);
    }
}