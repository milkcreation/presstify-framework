<?php

declare(strict_types=1);

namespace tiFy\Metabox\Contracts;

use Closure;
use League\Route\Http\Exception\NotFoundException;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Metabox\MetaboxDriverInterface;
use tiFy\Metabox\MetaboxContextInterface;
use tiFy\Metabox\MetaboxScreenInterface;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \tiFy\Support\Concerns\ContainerAwareTrait
 */
interface MetaboxContract
{
    /**
     * Récupération de l'instance courante.
     *
     * @return static
     */
    public static function instance(): MetaboxContract;

    /**
     * @param string $alias
     * @param string|array|MetaboxDriverInterface|Closure $driverDefinition Alias de qualification|Attributs de
     *     configuration|Instance
     * @param string|null $screen
     * @param string|null $context
     *
     * @return MetaboxContract
     */
    public function add(
        string $alias,
        $driverDefinition,
        string $screen,
        string $context
    ): MetaboxContract;

    /**
     * Récupération de la liste des pilotes assignés.
     *
     * @return MetaboxDriverInterface[]|array
     */
    public function all(): array;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MetaboxContract;

    /**
     * Récupération|Définition|Instance du gestionnaire de paramètres de configuration.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return ParamsBag|int|string|array|object
     */
    public function config($key = null, $default = null);

    /**
     * Repartition des éléments d'un écran. Ecran courant par défaut.
     *
     * @param string|null $screenAlias
     *
     * @return MetaboxContract
     */
    public function dispatch(?string $screenAlias = null): MetaboxContract;

    /**
     * Récupération d'un contexte assigné.
     *
     * @param string $alias
     *
     * @return MetaboxContextInterface|null
     */
    public function getContext(string $alias): ?MetaboxContextInterface;

    /**
     * Récupération d'un écran assigné.
     *
     * @param string $alias
     *
     * @return MetaboxScreenInterface|null
     */
    public function getScreen(string $alias): ?MetaboxScreenInterface;

    /**
     * Récupération de l'url de traitement des requêtes XHR.
     *
     * @param string $metabox Alias de qualification du pilote associé.
     * @param string|null $controller Nom de qualification du controleur de traitement de la requête XHR.
     * @param array $params Liste de paramètres complémentaire transmis dans l'url
     *
     * @return string
     */
    public function getXhrRouteUrl(string $metabox, ?string $controller = null, array $params = []): string;

    /**
     * Vérification d'existance d'un écran d'affichage.
     *
     * @param string $alias
     *
     * @return bool
     */
    public function hasScreen(string $alias): bool;

    /**
     * Déclaration d'un contexte d'affichage.
     *
     * @param string $alias
     * @param string|array|MetaboxContextInterface|null $contextDefinition
     *
     * @return static
     */
    public function registerContext(string $alias, $contextDefinition = null): MetaboxContract;

    /**
     * Déclaration d'un pilote de boîte de saisie.
     *
     * @param string $alias
     * @param string|array|MetaboxDriverInterface|null $driverDefinition
     *
     * @return static
     */
    public function registerDriver(string $alias, $driverDefinition = null): MetaboxContract;

    /**
     * Déclaration d'un pilote de boîte de saisie.
     *
     * @param string $alias
     * @param string|array|MetaboxScreenInterface|null $screenDefinition
     *
     * @return static
     */
    public function registerScreen(string $alias, $screenDefinition = null): MetaboxContract;

    /**
     * Récupération du rendu l'affichage des boîtes de saisies associées à un contexte d'un écran d'affichage.
     *
     * @param string $contextAlias
     * @param mixed ...$args
     *
     * @return string
     */
    public function render(string $contextAlias, ...$args): string;

    /**
     * Chemin absolu vers une ressources (fichier|répertoire).
     *
     * @param string|null $path Chemin relatif vers la ressource.
     *
     * @return LocalFilesystem|string|null
     */
    public function resources(?string $path = null);

    /**
     * Définition du contexte de base.
     *
     * @param string $baseContext
     *
     * @return static
     */
    public function setBaseContext(string $baseContext): MetaboxContract;

    /**
     * Définition du pilote de base.
     *
     * @param string $baseDriver
     *
     * @return static
     */
    public function setBaseDriver(string $baseDriver): MetaboxContract;

    /**
     * Définition de l'écran de base.
     *
     * @param string $baseScreen
     *
     * @return static
     */
    public function setBaseScreen(string $baseScreen): MetaboxContract;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): MetaboxContract;

    /**
     * Définition de l'écran d'affichage courant.
     *
     * @param string $screen
     *
     * @return static
     */
    public function setCurrentScreen(string $screen): MetaboxContract;

    /**
     * Déclaration d'un jeu de boîte de saisie boîte de saisie.
     *
     * @param string $screen Nom de qualification de l'écran d'affichage.
     * @param string $context Nom de qualification du contexte de l'écran d'affichage.
     * @param string[][]|array[][]|MetaboxDriverInterface[][] $driversDefinitions Liste des boîtes de saisie.
     *
     * @return static
     */
    public function stack(string $screen, string $context, array $driversDefinitions): MetaboxContract;

    /**
     * Répartiteur de traitement d'une requête XHR.
     *
     * @param string $metabox Alias de qualification du pilote associé.
     * @param string $controller Nom de qualification du controleur de traitement de la requête.
     * @param mixed ...$args Liste des arguments passés au controleur
     *
     * @return array
     *
     * @throws NotFoundException
     */
    public function xhrResponseDispatcher(string $metabox, string $controller, ...$args): array;
}
