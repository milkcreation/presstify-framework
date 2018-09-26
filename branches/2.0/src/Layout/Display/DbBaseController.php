<?php

namespace tiFy\Layout\Display;

use tiFy\Db\DbItemBaseController;
use tiFy\Contracts\Layout\LayoutDisplayDbInterface;
use tiFy\Contracts\Layout\LayoutDisplayInterface;

class DbBaseController extends DbItemBaseController implements LayoutDisplayDbInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutDisplayInterface
     */
    protected $layout;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs
     */
    public function __construct(LayoutDisplayInterface $layout)
    {
        $this->layout = $layout;

        parent::__construct($this->layout->name(), []);
    }
}