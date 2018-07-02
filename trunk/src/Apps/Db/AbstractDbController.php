<?php

namespace tiFy\Apps\Db;

use tiFy\Apps\AppControllerInterface;
use tiFy\Db\DbBaseController;

abstract class AbstractDbController extends DbBaseController implements DbInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var AppControllerInterface
     */
    protected $app;
}