<?php

namespace tiFy\App\Layout\Db;

use tiFy\App\AppInterface;
use tiFy\App\Db\AbstractAppDbController;
use tiFy\App\Layout\LayoutInterface;

class DbBaseController extends AbstractAppDbController implements DbInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;
}