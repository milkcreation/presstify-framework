<?php

namespace tiFy\Kernel;

use BadMethodCallException;
use Exception;
use Interop\Container\ContainerInterface;
use tiFy\Container\Container;

class Application extends Container
{
    /**
     * Instance du conteneur d'injection de dépendances.
     * @var ContainerInterface
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->delegate($container);

        parent::__construct();
    }

    /**
     * Délégation d'appel des méthodes du conteneur d'injection.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        try {
            return $this->container->$name(...$arguments);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(__('La méthode %s n\'est pas disponible.', 'tify'), $name));
        }
    }

    /**
     * Compatibilité corcel.
     *
     * @return string
     */
    public function version(): string
    {
        return 'tiFy';
    }

    /**
     * @inheritdoc
     */
    public function getServiceProviders(): array
    {
        return config('app.providers', []);
    }
}