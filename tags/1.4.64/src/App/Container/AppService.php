<?php

namespace tiFy\App\Container;

use League\Container\Definition\DefinitionInterface;
use tiFy\Kernel\Container\Service;
use tiFy\Contracts\App\AppInterface;
use tiFy\tiFy;

class AppService extends Service
{
    /**
     * Classe de rappel du controleur de l'interface associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $abstract Nom de qualification du service.
     * @param array $attrs Attributs de configuration.
     * @param AppInterface Classe de rappel du controleur de l'interface associée.
     *
     * @return void
     */
    public function __construct($abstract, $attrs = [], AppInterface $app)
    {
        $this->app = $app;

        parent::__construct($abstract, $attrs, tiFy::instance());
    }

    /**
     * {@inheritdoc}
     */
    public function build($args = [])
    {
        if ($this->isSingleton() && $this->resolved()) :
            return $this->instance;
        endif;

        if (!$this->definition) :
            $this->bind();
        endif;

        array_push($args, $this->app);

        return $this->instance =  ($this->definition instanceof DefinitionInterface)
            ? $this->definition->build($args)
            : $this->definition;
    }
}