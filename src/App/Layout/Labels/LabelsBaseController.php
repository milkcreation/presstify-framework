<?php

namespace tiFy\App\Layout\Labels;

use tiFy\Contracts\App\AppInterface;
use tiFy\App\Labels\AbstractAppLabelsController;
use tiFy\App\Layout\LayoutInterface;

class LabelsBaseController extends AbstractAppLabelsController implements LabelsInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;
}