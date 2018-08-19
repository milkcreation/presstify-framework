<?php

namespace tiFy\Apps\Layout\Labels;

use tiFy\Apps\AppInterface;
use tiFy\Apps\Labels\AbstractAppLabelsController;
use tiFy\Apps\Layout\LayoutInterface;

class LabelsBaseController extends AbstractAppLabelsController implements LabelsInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;
}