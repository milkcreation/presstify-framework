<?php

namespace tiFy\App\Collection;

use Illuminate\Support\Collection;
use tiFy\App\AppInterface;

class AbstractAppCollection extends Collection
{
    /**
     * Classe de rappel du controleur de l'application associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * Instanciation.
     *
     * @param AppInterface $app
     *
     * @return $this
     */
    public function __invoke($app)
    {
        $this->app = $app;

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;

        return $this;
    }
}