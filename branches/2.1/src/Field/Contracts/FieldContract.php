<?php declare(strict_types=1);

namespace tiFy\Field\Contracts;

use Closure;
use League\Route\Http\Exception\NotFoundException;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Field\FieldDriverInterface;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \tiFy\Support\Concerns\ContainerAwareTrait
 */
interface FieldContract
{
    /**
     * Récupération de l'instance courante.
     *
     * @return static
     */
    public static function instance(): FieldContract;

    /**
     * Récupération de la liste des pilote déclarés.
     *
     * @return FieldDriverInterface[][]
     */
    public function all(): array;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): FieldContract;

    /**
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return ParamsBag|int|string|array|object
     */
    public function config($key = null, $default = null);

    /**
     * Récupération d'un champ déclaré.
     *
     * @param string $alias Alias de qualification.
     * @param string|array|null $idOrParams Identifiant de qualification ou paramètres de configuration.
     * @param array|null $params Liste des paramètres de configuration.
     *
     * @return FieldDriverInterface|null
     */
    public function get(string $alias, $idOrParams = null, ?array $params = []): ?FieldDriverInterface;

    /**
     * Récupération de l'url de traitement des requêtes XHR.
     *
     * @param string $field Alias de qualification du pilote associé.
     * @param string|null $controller Nom de qualification du controleur de traitement de la requête XHR.
     * @param array $params Liste de paramètres complémentaire transmis dans l'url
     *
     * @return string
     */
    public function getXhrRouteUrl(string $field, ?string $controller = null, array $params = []): string;

    /**
     * Déclaration d'un pilote.
     *
     * @param string $alias
     * @param string|FieldDriverInterface|Closure $driverDefinition
     * @param Closure|null $callback
     *
     * @return FieldContract
     */
    public function register(string $alias, $driverDefinition, ?Closure $callback = null): FieldContract;

    /**
     * Déclaration des instances de pilotes par défaut.
     *
     * @return static
     */
    public function registerDefaultDrivers(): FieldContract;

    /**
     * Chemin absolu vers une ressources (fichier|répertoire).
     *
     * @param string|null $path Chemin relatif vers la ressource.
     *
     * @return LocalFilesystem|string|null
     */
    public function resources(?string $path = null);

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): FieldContract;

    /**
     * Répartiteur de traitement d'une requête XHR.
     *
     * @param string $field Alias de qualification du pilote associé.
     * @param string $controller Nom de qualification du controleur de traitement de la requête.
     * @param mixed ...$args Liste des arguments passés au controleur
     *
     * @return array
     *
     * @throws NotFoundException
     */
    public function xhrResponseDispatcher(string $field, string $controller, ...$args): array;
}