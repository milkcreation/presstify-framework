<?php

namespace tiFy\Apps\Item;

use tiFy\Apps\AppInterface;
use tiFy\Kernel\Item\AbstractItemIterator;

abstract class AbstractAppItemIterator extends AbstractItemIterator
{
    /**
     * Classe de rappel du controleur de l'application associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des paramètres personnalisés.
     * @param AppInterface $app  Classe de rappel du controleur de l'application.
     *
     * @return void
     */
    public function __construct($attrs = [], AppInterface $app)
    {
        $this->app = $app;

        parent::__construct($attrs);

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;
    }
}