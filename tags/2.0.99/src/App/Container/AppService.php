<?php

namespace tiFy\App\Container;

use League\Container\Definition\DefinitionInterface;
use League\Container\Argument\RawArgument;
use Psr\Container\ContainerInterface;
use tiFy\Support\ParamsBag;

class AppService extends ParamsBag
{
    /**
     * Nom de qualification du service.
     * @var string
     */
    protected $abstract;

    /**
     * Liste des variables passées en argument.
     * @var array
     */
    protected $args = [];

    /**
     * Classe de rappel du conteneur de services.
     * @var ContainerInterface
     */
    protected $container;

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
     * @param AppContainer $container Classe de rappel du conteneur de services.
     *
     * @return void
     */
    public function __construct($abstract, $attrs = [], $container)
    {
        $this->abstract = $abstract;
        $this->container = $container;

        $this->set($attrs)->parse();

        $this->bind();
    }

    /**
     * @inheritdoc
     */
    public function build($args = [])
    {
        if ($this->isSingleton() && $this->resolved()) :
            return $this->instance;
        endif;

        if (!$this->definition) :
            $this->bind();
        endif;

        foreach($args as &$arg) :
            $arg = new RawArgument($arg);
        endforeach;

        array_push($args, $this->container);

        return $this->instance =  ($this->definition instanceof DefinitionInterface)
            ? $this->definition->build($args)
            : $this->definition;
    }

    /**
     * @inheritdoc
     */
    public function bind()
    {
        return $this->definition = $this->getContainer()->add($this->getAlias(), $this->getConcrete(), $this->isSingleton());
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @inheritdoc
     */
    public function getAlias()
    {
        return $this->get('alias');
    }

    /**
     * @inheritdoc
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @inheritdoc
     */
    public function getConcrete()
    {
        return $this->get('concrete');
    }

    /**
     * @inheritdoc
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @inheritdoc
     */
    public function isBootable()
    {
        return !empty($this->get('bootable'));
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function isDeferred()
    {
        return empty($this->get('bootable'));
    }

    /**
     * @inheritdoc
     */
    public function isSingleton()
    {
        return !empty($this->get('singleton'));
    }

    /**
     * @inheritdoc
     */
    public function setArgs($args = [])
    {
        return $this->set('args', $args);
    }

    /**
     * @inheritdoc
     */
    public function resolved()
    {
        return !empty($this->instance) && $this->definition;
    }
}