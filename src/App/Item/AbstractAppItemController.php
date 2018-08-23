<?php

namespace tiFy\App\Item;

use tiFy\App\AppInterface;
use tiFy\Contracts\App\Item\AppItemInterface;
use tiFy\Kernel\Item\AbstractItemController;

abstract class AbstractAppItemController extends AbstractItemController implements AppItemInterface
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
     * @param AppInterface $app Classe de rappel du controleur de l'application.
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