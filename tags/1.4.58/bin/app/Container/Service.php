<?php

namespace tiFy\App\Container;

use League\Container\Definition\DefinitionInterface;
use tiFy\App\AppControllerInterface;
use tiFy\App\Item\AbstractAppItemIterator;

class Service extends AbstractAppItemIterator implements ServiceInterface
{
    /**
     * Classe de rappel du controleur de l'interface associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Nom de qualification du service.
     * @var string
     */
    protected $abstract;

    /**
     * Définition du service déclaré.
     * @var DefinitionInterface
     */
    protected $definition;

    /**
     * Instance courante du service.
     * @var mixed
     */
    protected $instance;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $abstract Nom de qualification du service.
     * @param array $attrs Attributs de configuration.
     * @param AppControllerInterface Classe de rappel du controleur de l'interface associée.
     *
     * @return void
     */
    public function __construct($abstract, $attrs = [], AppControllerInterface $app)
    {
        $this->abstract = $abstract;

        parent::__construct($attrs, $app);
    }

    /**
     * {@inheritdoc}
     */
    public function bind()
    {
        return $this->definition = $this->app->appServiceAdd($this->getAbstract(), $this->getConcrete(), $this->isSingleton());
    }

    /**
     * {@inheritdoc}
     */
    public function build($args = [])
    {
        if ($this->isSingleton() && $this->resolved()) :
            return $this->instance;
        endif;

        if (!$this->resolved()) :
            $this->bind();
        endif;

        array_push($args, $this->app);

        return $this->instance = $this->definition->build($args);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'alias'     => $this->abstract,
            'args'      => [],
            'bootable'  => false,
            'concrete'  => null,
            'singleton' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->get('alias');
    }

    /**
     * {@inheritdoc}
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * {@inheritdoc}
     */
    public function getConcrete()
    {
        return $this->get('concrete');
    }

    /**
     * {@inheritdoc}
     */
    public function isBootable()
    {
        return !empty($this->get('bootable'));
    }

    /**
     * {@inheritdoc}
     */
    public function isClosure()
    {
        try {
            $reflection = new \ReflectionFunction($this->getConcrete());
            return $reflection->isClosure();
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isDeferred()
    {
        return empty($this->get('bootable'));
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleton()
    {
        return !empty($this->get('singleton'));
    }

    /**
     * {@inheritdoc}
     */
    public function setArgs($args = [])
    {
        return $this->set('args', $args);
    }

    /**
     * {@inheritdoc}
     */
    public function resolved()
    {
        return !empty($this->instance);
    }
}