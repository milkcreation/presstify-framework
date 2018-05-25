<?php

namespace tiFy\Kernel;

use League\Plates\Engine;

class Templates extends Engine
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $directory = null;
        $fileExtension = 'php';

        parent::__construct($directory, $fileExtension);
    }
}