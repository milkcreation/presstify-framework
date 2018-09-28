<?php

namespace tiFy\Layout\Display;

use tiFy\Contracts\Layout\LayoutDisplayParamsInterface;
use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Kernel\Parameters\AbstractParametersBag;

class ParamsBaseController extends AbstractParametersBag implements LayoutDisplayParamsInterface
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

        parent::__construct($this->layout->get('params', []));
    }
}