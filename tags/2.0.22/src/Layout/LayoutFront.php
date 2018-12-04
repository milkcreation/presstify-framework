<?php

namespace tiFy\Layout;

use tiFy\Contracts\Layout\LayoutFrontFactoryInterface;

final class LayoutFront
{
    /**
     * Liste des éléments déclarés.
     * @var LayoutFrontFactoryInterface[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function () {
                foreach(config('layout.front', []) as $name => $attrs) :
                    $this->register($name, $attrs);
                endforeach;
            },
            999999
        );
    }

    /**
     * Déclaration d'une disposition.
     *
     * @param string $name Nom de qualification de la disposition.
     * @param array $attrs Liste des attributs de configuration de la disposition.
     *
     * @return LayoutFrontFactoryInterface
     */
    protected function register($name, $attrs = [])
    {
        if (isset($this->items[$name])) :
            return $this->items[$name];
        endif;

        return $this->items[$name] = app('layout.front.factory', [$name, $attrs]);
    }
}