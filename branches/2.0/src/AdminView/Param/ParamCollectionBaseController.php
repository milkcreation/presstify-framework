<?php

namespace tiFy\AdminView\Param;

use tiFy\AdminView\AdminViewControllerInterface;
use tiFy\Apps\Attributes\AbstractAttributesController;

class ParamCollectionBaseController extends AbstractAttributesController implements ParamCollectionInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
     * @var AdminViewControllerInterface
     */
    protected $app;
}