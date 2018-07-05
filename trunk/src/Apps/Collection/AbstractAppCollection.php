<?php

namespace tiFy\Apps\Collection;

use tiFy\Apps\AppControllerInterface;
use tiFy\Kernel\Collection\AbstractCollection;

class AbstractAppCollection extends AbstractCollection
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