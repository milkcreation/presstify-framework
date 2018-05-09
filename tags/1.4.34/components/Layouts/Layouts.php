<?php

namespace tiFy\Components\Layouts;

use tiFy\App\Component;

class Layouts extends Component
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        require $this->appDirname() . '/Helpers.php';
    }
}