<?php

namespace tiFy\Core\MetaTag;

use tiFy\App\Traits\App as TraitsApp;

class Favicon
{
    use TraitsApp;

    /**
     * Classe de rappel de la classe courante
     * @var static
     */
    protected static $instance;

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __construct()
    {
        $this->attributes = $this->parse();
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Initialisation
     *
     * @return self
     */
    final public static function make()
    {
        if (!self::$instance) :
            self::$instance = new static();
        endif;

        return self::$instance;
    }
}