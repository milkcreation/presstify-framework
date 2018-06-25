<?php

namespace tiFy\Apps\ServiceProvider;

use tiFy\Apps\Attributes\AbstractAttributesIterator;

class ProviderItem extends AbstractAttributesIterator
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
     * @var AppControllerInterface
     */
    protected $app;

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
    public function isClosure()
    {
        try {
            $reflection = new ReflectionFunction($this->getConcrete());
            return $reflection->isClosure();
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleton()
    {
        return !empty($this->get('singleton'));
    }
}