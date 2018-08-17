<?php

namespace tiFy\App\Collection;

use Illuminate\Support\Collection;
use tiFy\App\AppControllerInterface;

class AbstractAppCollection extends Collection
{
    /**
     * Classe de rappel du controleur de l'application associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Instanciation.
     *
     * @param AppControllerInterface $app
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