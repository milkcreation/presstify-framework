<?php

namespace tiFy\Apps\Layout\Db;

use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\Db\AbstractDbController;
use tiFy\Apps\Layout\LayoutControllerInterface;

class DbBaseController extends AbstractDbController implements DbControllerInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutControllerInterface
     */
    protected $app;
}