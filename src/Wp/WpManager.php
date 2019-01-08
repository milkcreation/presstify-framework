<?php

namespace tiFy\Wp;

use tiFy\Contracts\Wp\WpManager as WpManagerContract;

class WpManager implements WpManagerContract
{
    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        if ($this->is()) :
            config(['site_url' => site_url()]);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function is()
    {
        return true;
    }
}