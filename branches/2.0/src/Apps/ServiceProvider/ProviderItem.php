<?php

namespace tiFy\Apps\ServiceProvider;

use tiFy\Apps\Attributes\AbstractAttributesIterator;
use tiFy\Apps\AppControllerInterface;

class ProviderItem extends AbstractAttributesIterator implements ProviderItemInterface
{
    /**
     * Classe de rappel du controleur de l'interface associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Indicateur d'instanciation.
     * @var bool
     */
    protected $instanciated = false;

    /**
     * Nom de qualification du service.
     * @var string|int
     */
    protected $name;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du service.
     * @param array $attrs Attributs de configuration.
     * @param AppControllerInterface Classe de rappel du controleur de l'interface associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], AppControllerInterface $app)
    {
        $this->name = $name;

        parent::__construct($attrs, $app);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'alias'     => '',
            'concrete'  => '',
            'bootable'  => false,
            'singleton' => false,
            'args'      => []
        ];
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
        return $this->get('args', []);
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
    public function getName()
    {
        return $this->name;
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
    public function isInstanciated()
    {
        return !empty($this->instanciated);
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
    public function setInstanciated()
    {
        return $this->instanciated = true;
    }
}