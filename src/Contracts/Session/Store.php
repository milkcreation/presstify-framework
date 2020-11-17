<?php declare(strict_types=1);

namespace tiFy\Contracts\Session;

use Illuminate\Database\Query\Builder as DbBuilder;
use tiFy\Contracts\Log\Logger;

interface Store
{
    /**
     * Récupération de la liste des attributs de session.
     *
     * @return array
     */
    public function all(): array;

    /**
     * @inheritDoc
     */
    public function db(): DbBuilder;

    /**
     * Destruction de la session.
     *
     * @return static
     */
    public function destroy(): Store;

    /**
     * Récupération de la prochaine date de définition d'expiration.
     *
     * @return int
     */
    public function expiration(): int;

    /**
     * Suppression de la liste des attributs de session.
     *
     * @return void
     */
    public function flush(): void;

    /**
     * Récupération d'un attribut de session.
     *
     * @param  string $key Clé d'indice d'un attribut.
     * @param  mixed  $default Valeur de retour par défaut.
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Récupération d'attributs d'identification de session.
     *
     * @param string|array|null $keys Liste des attributs.
     * session_key|session_expiration|session_expiring|cookie_hash. Renvoi tout si vide.
     *
     * @return mixed
     */
    public function getCredentials($keys = null);

    /**
     * Récupération du hashage.
     *
     * @param int $expire Timestamp d'expiration du cookie
     *
     * @return string
     */
    public function getHash(int $expire): string;

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Récupération du nom de qualification de la session
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Suppression d'attributs de session.
     *
     * @param  string|string[]  $keys
     * @return void
     */
    public function forget($keys): void;

    /**
     * Vérification d'existance d'un attribut de session.
     *
     * @param string $key Clé d'indice de l'attribut
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Récupération de l'instance du gestionnaire de journalisation.
     *
     * @return Logger
     */
    public function logger(): Logger;

    /**
     * Récupération d'une liste d'attributs de session basée sur un jeu de clés d'indices.
     *
     * @param string[] $keys
     * @return array
     */
    public function only(array $keys): array;

    /**
     * Récupération d'un attribut et suppression.
     *
     * @param string $key Clé d'indice
     * @param  mixed  $default Valeur de retour par défaut
     *
     * @return mixed
     */
    public function pull(string $key, $default = null);

    /**
     * Insertion d'une valeur d'attribut complémentaire.
     *
     * @param string $key Clé d'indice
     * @param  mixed $value Valeur d'affectation
     * @return void
     */
    public function push(string $key, $value): void;

    /**
     * Définition de données de session.
     *
     * @param string|array $key Identifiant de qualification de la variable
     * @param mixed $value Valeur de la variable
     *
     * @return static
     */
    public function put($key, $value = null): Store;

    /**
     * Définition d'une donnée de session.
     *
     * @param string $key Clé d'indice
     * @param mixed $value Valeur de la variable
     *
     * @return static
     */
    public function putOne(string $key, $value = null): Store;

    /**
     * Récupération de la liste des variables de session enregistrés en base.
     *
     * @return array
     */
    public function read(): array;

    /**
     * Sauvegarde des données de session.
     *
     * @return static
     */
    public function save(): Store;

    /**
     * Récupération de l'instance du gestionnaire de session associé.
     *
     * @return Session
     */
    public function session(): Session;

    /**
     * Définition de l'indice de la clé de stockage des données de session.
     *
     * @param string|null $key
     *
     * @return static
     */
    public function setKey(?string $key = null): Store;

    /**
     * Définition du gestionnaire de journalisation.
     *
     * @param Logger $logger
     *
     * @return static
     */
    public function setLogger(Logger $logger): Store;

    /**
     * Définition du nom de qualification de la session.
     *
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): Store;

    /**
     * Initialisation de l'instance.
     *
     * @return static
     */
    public function start(): Store;


    /**
     * Mise à jour de la date d'expiration de la session en base.
     *
     * @param int $expiration Timestamp d'expiration de la session.
     *
     * @return static
     */
    public function updateStoredExpiration(int $expiration): Store;
}