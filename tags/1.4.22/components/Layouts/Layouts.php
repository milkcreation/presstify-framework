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
        exit;
        require $this->appDirname() . '/Helpers.php';
    }
}