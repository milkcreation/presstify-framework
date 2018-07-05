<?php

namespace tiFy\Apps\Item;

use tiFy\Apps\AppControllerInterface;
use tiFy\Kernel\Item\AbstractItemIterator;

abstract class AbstractAppItemIterator extends AbstractItemIterator
{
    /**
     * Classe de rappel du controleur de l'application associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des paramètres personnalisés.
     * @param AppControllerInterface $app  Classe de rappel du controleur de l'application.
     *
     * @return void
     */
    public function __construct($attrs = [], AppControllerInterface $app)
    {
        $this->app = $app;

        parent::__construct($attrs);

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;
    }
}