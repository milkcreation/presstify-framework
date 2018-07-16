<?php

namespace tiFy\Apps\Layout\Db;

use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\Db\AbstractDbController;
use tiFy\Apps\Layout\LayoutInterface;

class DbBaseController extends AbstractDbController implements DbInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;
}