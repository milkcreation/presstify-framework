<?php declare(strict_types=1);

namespace tiFy\Contracts\Field;

use Exception;
use InvalidArgumentException;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;

interface Field
{
    /**
     * Récupération de l'instance courante.
     *
     * @return static
     *
     * @throws Exception
     */
    public static function instance(): Field;

    /**
     * Initialisation.
     *
     * @return static
     *
     * @throws Exception
     */
    public function boot(): Field;

    /**
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return mixed|ParamsBag
     */
    public function config($key = null, $default = null);

    /**
     * Récupération d'un champ déclaré.
     *
     * @param string $alias Alias de qualification.
     * @param string|array|null $idOrAttrs Identifiant de qualification ou attributs de configuration.
     * @param array|null $attrs Liste des attributs de configuration.
     *
     * @return FieldDriver|null
     *
     * @throws InvalidArgumentException
     */
    public function get(string $alias, $idOrAttrs = null, ?array $attrs = null): ?FieldDriver;

    /**
     * Récupération de l'instance du gestionnaire d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

    /**
     * Déclaration d'un pilote.
     *
     * @param string $alias
     * @param FieldDriver $driver
     *
     * @return FieldDriver
     *
     * @throws Exception
     */
    public function register(string $alias, FieldDriver $driver): FieldDriver;

    /**
     * Déclaration des instances de pilotes par défaut.
     *
     * @return static
     *
     * @throws Exception
     */
    public function registerDefaultDrivers(): Field;

    /**
     * Résolution de service fourni par le gestionnaire.
     *
     * @param string $alias
     *
     * @return object|mixed|null
     */
    public function resolve(string $alias);

    /**
     * Vérification de résolution possible d'un service fourni par le gestionnaire.
     *
     * @param string $alias
     *
     * @return bool
     */
    public function resolvable(string $alias): bool;

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
    public function setConfig(array $attrs): Field;

    /**
     * Définition du conteneur d'injection de dépendances.
     *
     * @param Container $container
     *
     * @return static
     */
    public function setContainer(Container $container): Field;
}