<?php declare(strict_types=1);

namespace tiFy\Routing\Concerns;

use Psr\Container\ContainerInterface;
use tiFy\Contracts\Routing\ContainerAwareTrait as ContainerAwareTraitContract;

trait ContainerAwareTrait
{
    /**
     * Instance du conteneur d'injection de dépendances.
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inheritDoc
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     *
     * @return ContainerAwareTrait
     */
    public function setContainer(ContainerInterface $container): ContainerAwareTraitContract
    {
        $this->container = $container;

        return $this;
    }
}