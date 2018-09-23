<?php

namespace tiFy\Kernel\Container;

use League\Container\Definition\DefinitionInterface;
use tiFy\Contracts\Container\ServiceInterface;
use tiFy\Kernel\Container\Container;
use tiFy\Kernel\Item\AbstractItemIterator;

class Service extends AbstractItemIterator implements ServiceInterface
{
    /**
     * Classe de rappel du conteneur de services.
     * @var Container
     */
    protected $container;

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
     * @param Container $container Classe de rappel du conteneur de services.
     *
     * @return void
     */
    public function __construct($abstract, $attrs = [], Container $container)
    {
        $this->abstract = $abstract;
        $this->container = $container;

        parent::__construct($attrs);

        $this->bind();
    }

    /**
     * {@inheritdoc}
     */
    public function bind()
    {
        return $this->definition = $this->getContainer()->add($this->getAlias(), $this->getConcrete(), $this->isSingleton());
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

        return $this->instance =  ($this->definition instanceof DefinitionInterface)
            ? $this->definition->build($args)
            : $this->definition;
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
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
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
        return !empty($this->instance) && $this->definition;
    }
}