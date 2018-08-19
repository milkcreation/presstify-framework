<?php

namespace tiFy\Apps\Collection;

use tiFy\Apps\AppInterface;
use tiFy\Kernel\Collection\AbstractCollection;

abstract class AbstractAppCollection extends AbstractCollection
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