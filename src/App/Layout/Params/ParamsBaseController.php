<?php

namespace tiFy\App\Layout\Params;

use tiFy\App\Item\AbstractAppItemController;
use tiFy\App\Layout\LayoutInterface;

class ParamsBaseController extends AbstractAppItemController implements ParamsInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;
}