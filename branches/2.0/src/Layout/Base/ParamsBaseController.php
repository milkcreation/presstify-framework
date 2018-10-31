<?php

namespace tiFy\Layout\Base;

use tiFy\Contracts\Layout\LayoutDisplayParamsInterface;
use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Kernel\Params\ParamsBag;

class ParamsBaseController extends ParamsBag implements LayoutDisplayParamsInterface
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