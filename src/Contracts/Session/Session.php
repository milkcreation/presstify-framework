<?php declare(strict_types=1);

namespace tiFy\Contracts\Session;

use Countable;
use IteratorAggregate;
use Psr\Container\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Session\SessionInterface as BaseSession;
use tiFy\Contracts\FlashBag;

interface Session extends BaseSession, Countable, IteratorAggregate
{
    /**
     * Récupération de l'instance du gestionnaire de session éphémère|ajout d'attributs|récupération d'attributs.
     *
     * @param string|array|null $key
     * @param mixed $value
     *
     * @return FlashBag|Session|mixed|null
     */
    public function flash($key = null, $value = null);

    /**
     * Conservation des données de session éphèmere pour la requête suivante.
     *
     * @param array|null $keys Personnalisation de la liste des clé d'indices à conserver.
     *
     * @return static
     */
    public function reflash(?array $keys = null): Session;

    /**
     * Déclaration d'une session de stockage des données.
     *
     * @param string $name Nom de qualification de la session.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return Store|null
     */
    public function registerStore(string $name, array $attrs = []): ?Store;

    /**
     * Définition du conteneur d'injection de dépendances.
     *
     * @param Container $container
     *
     * @return static
     */
    public function setContainer(Container $container): Session;

    /**
     * Récupération d'une session.
     *
     * @param string $name Nom de qualification de la session.
     *
     * @return Store|null
     */
    public function store(string $name): ?Store;
}