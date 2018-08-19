<?php

namespace tiFy\Apps\Layout\Db;

use tiFy\Apps\AppInterface;
use tiFy\Apps\Db\AbstractAppDbController;
use tiFy\Apps\Layout\LayoutInterface;

class DbBaseController extends AbstractAppDbController implements DbInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;
}