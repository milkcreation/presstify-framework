<?php

namespace tiFy\Apps\Layout\Labels;

use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\Labels\AbstractLabelsController;
use tiFy\Apps\Layout\LayoutInterface;

class LabelsBaseController extends AbstractLabelsController implements LabelsInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;
}