<?php

namespace tiFy\App\Db;

use tiFy\Contracts\App\AppInterface;
use tiFy\Db\DbBaseController;

abstract class AbstractAppDbController extends DbBaseController implements AppDbInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var AppInterface
     */
    protected $app;
}