<?php

namespace tiFy\Apps;


abstract class AppController
{
    use AppTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        if (! $this->appExists($this)) :
            $this->appRegister($this);
        endif;

        if (did_action('tify_app_boot')) :
            $this->boot();
        else :
            add_action('tify_app_boot', [$this, 'boot']);
        endif;
    }

    /**
     *
     */
    public function boot()
    {
        
    }
}