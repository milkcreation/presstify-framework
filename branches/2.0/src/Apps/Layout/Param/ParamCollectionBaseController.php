<?php

namespace tiFy\Apps\Layout\Param;

use tiFy\Apps\Layout\LayoutControllerInterface;
use tiFy\Apps\Item\AbstractAppItemController;

class ParamCollectionBaseController extends AbstractAppItemController implements ParamCollectionInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutControllerInterface
     */
    protected $app;
}