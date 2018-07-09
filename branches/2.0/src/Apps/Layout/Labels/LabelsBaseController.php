<?php

namespace tiFy\Apps\Layout\Labels;

use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\Labels\AbstractLabelsController;
use tiFy\Apps\Layout\LayoutControllerInterface;

class LabelsBaseController extends AbstractLabelsController implements LabelsControllerInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutControllerInterface
     */
    protected $app;
}