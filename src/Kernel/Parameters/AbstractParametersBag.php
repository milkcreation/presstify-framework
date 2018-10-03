<?php

namespace tiFy\Kernel\Parameters;

use tiFy\Contracts\Kernel\ParametersBagInterface;

abstract class AbstractParametersBag implements ParametersBagInterface
{
    use ParametersBagTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des paramètres personnalisés.
     *
     * @return void
     */
    public function __construct($attrs = [])
    {
        $this->parse($attrs);
    }
}