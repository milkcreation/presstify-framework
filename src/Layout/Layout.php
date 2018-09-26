<?php

namespace tiFy\Layout;

class Layout
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     *
     */
    public function resourcesDir($path)
    {
        $cinfo = class_info($this);
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists($cinfo->getDirname() . $path) ? class_info($this)->getDirname() . $path : '';
    }
}