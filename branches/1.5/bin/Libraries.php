<?php

namespace tiFy;

use tiFy\App;

class Libraries extends App
{
    /**
     * CONSTRUCTEUR
     * 
     * @return void
     */
    public function __construct()
    {
        $this->tFyClassLoad('tiFy\Lib', $this->tFyAbsDir() .'/bin/lib');
        foreach (glob($this->tFyAbsDir() . '/bin/lib/*', GLOB_ONLYDIR) as $dirname) :
            $this->tFyClassLoad('tiFy\Lib', $dirname);
        endforeach;
        
        /**
         * Librairies Tierces
         */
        /**
         * Emojione
         */
        $this->tFyClassLoad('Emojione', $this->tFyAbsDir() . '/bin/lib/Emojione');
    }
}