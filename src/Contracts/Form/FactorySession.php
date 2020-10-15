<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use BadMethodCallException;
use tiFy\Contracts\Session\Store;

/**
 * @mixin \tiFy\Session\Store
 */
interface FactorySession extends FactoryResolver
{
    /**
     * Délégation d'appel des méthodes du controleur de données de session associé.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call(string $name, array $arguments);

    /**
     * Récupération du jeton de qualification.
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Récupération de l'instance du gestionnaire de session associé.
     *
     * @return Store
     */
    public function store(): Store;
}