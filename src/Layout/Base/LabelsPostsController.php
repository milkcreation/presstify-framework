<?php

namespace tiFy\Layout\Base;

use tiFy\Contracts\Layout\LayoutDisplayLabelsInterface;
use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\PostType\PostTypeItemLabelsController;

class LabelsPostsController extends PostTypeItemLabelsController implements LayoutDisplayLabelsInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutDisplayInterface
     */
    protected $layout;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs
     */
    public function __construct(LayoutDisplayInterface $layout)
    {
        $this->layout = $layout;

        parent::__construct($this->layout->name(), []);
    }
}