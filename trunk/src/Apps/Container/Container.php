<?php

namespace tiFy\Apps\Container;

use Illuminate\Support\Collection;
use tiFy\Apps\AppController;

class Container extends AppController
{
    /**
     * Liste des services déclarés.
     * @var array
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        foreach ($this->appConfig('providers', []) as $provider) :
            $concrete = new $provider($this);

            if ($concrete instanceof ServiceProvider) :
                $this->appServiceProvider($concrete);

                $this->items += $concrete->all();
            endif;
        endforeach;
    }

    /**
     * Récupération d'un service fourni.
     *
     * @param string $alias Identifiant de qualification du controleur.
     * @param null|array $args Liste des variables passés en argument.
     *
     * @return object
     */
    public function resolve($alias, $args = [])
    {
        array_push($args, $this);

        return $this->appServiceGet($alias, $args);
    }
}