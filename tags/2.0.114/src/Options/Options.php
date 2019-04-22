<?php

namespace tiFy\Options;

use Psr\Container\ContainerInterface;
use tiFy\Options\Page\OptionsPage;

class Options
{
    /**
     * Instance de conteneur d'injection de dépendances.
     *
     */
    protected $container;

    /**
     * Liste des éléments.
     * @var OptionsPage[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        add_action('init', function () {
            foreach(config('options', []) as $name => $attrs) {
                $this->items[$name] = new OptionsPage($name, $attrs);
            }
            if (!isset($this->items['tify_options'])) {
                $this->items['tify_options'] = new OptionsPage('tify_options', []);
            }
        });
    }
}