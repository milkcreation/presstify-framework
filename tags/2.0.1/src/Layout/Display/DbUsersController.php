<?php

namespace tiFy\Layout\Display;

use tiFy\User\Db\DbUsersController as UserDbController;
use tiFy\Contracts\Layout\LayoutDisplayDbInterface;
use tiFy\Contracts\Layout\LayoutDisplayInterface;

class DbUsersController extends UserDbController implements LayoutDisplayDbInterface
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