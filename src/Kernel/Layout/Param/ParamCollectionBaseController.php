<?php

namespace tiFy\Kernel\Layout\Param;

use tiFy\Kernel\Layout\LayoutControllerInterface;
use tiFy\Apps\Item\AbstractAppItemController;

class ParamCollectionBaseController extends AbstractAppItemController implements ParamCollectionInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutControllerInterface
     */
    protected $app;
}