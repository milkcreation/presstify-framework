<?php

namespace tiFy\Apps\Layout\Params;

use tiFy\Apps\Item\AbstractAppItemController;
use tiFy\Apps\Layout\LayoutInterface;

class ParamsBaseController extends AbstractAppItemController implements ParamsInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;
}