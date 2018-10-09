<?php

namespace tiFy\Layout;

use tiFy\Contracts\Layout\LayoutFactoryFrontInterface;
use tiFy\Contracts\Layout\LayoutItemInterface;

class LayoutFactoryFront extends AbstractLayoutFactory implements LayoutFactoryFrontInterface
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *
     *      @param string|callable $content Classe de rappel du controleur d'affichage.
     *      @param array $params Liste des paramètres.
     * }
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification de la disposition associée.
     * @param array $attrs Attributs de configuration de la disposition associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        parent::__construct($name, $attrs);

        $this->add_action(
            'wp_loaded',
            function () {
                if ($this->layout() instanceof LayoutItemInterface) :
                    $this->layout()->load();
                endif;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isAdmin()
    {
        return false;
    }
}