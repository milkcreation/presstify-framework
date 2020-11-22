<?php declare(strict_types=1);

namespace tiFy\Contracts\Metabox;

use Exception;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;

interface Metabox
{
    /**
     * Récupération de l'instance courante.
     *
     * @return static
     *
     * @throws Exception
     */
    public static function instance(): Metabox;

    /**
     * Ajout d'un pilote.
     *
     * @param string $alias
     * @param string|array|MetaboxDriver|null $driver Alias de qualification|Attributs de configuration|Instance
     *
     * @return MetaboxDriver
     *
     * @throws Exception
     */
    public function add(string $alias, $driver = null): MetaboxDriver;

    /**
     * Ajout d'un contexte.
     *
     * @param string $alias
     * @param string|array|MetaboxContext|null $context Alias de qualification|Attributs de configuration|Instance
     *
     * @return MetaboxContext
     *
     * @throws Exception
     */
    public function addContext(string $alias, $context = null): MetaboxContext;

    /**
     * Ajout d'un écran.
     *
     * @param string $alias
     * @param string|array|MetaboxScreen|null $screen Alias de qualification|Attributs de configuration|Instance
     *
     * @return MetaboxScreen
     *
     * @throws Exception
     */
    public function addScreen(string $alias, $screen = null): MetaboxScreen;

    /**
     * Récupération de la liste des pilotes assignés.
     *
     * @return MetaboxDriver[]|array
     */
    public function all(): array;

    /**
     * Initialisation.
     *
     * @return static
     *
     * @throws Exception
     */
    public function boot(): Metabox;

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
     * Recupère les éléments de rendu selon un contexte associé dans un écran d'affichage.
     * {@internal Utilise l'écran d'affichage courant, si l'écran d'affichage n'est pas défini.}
     *
     * @param MetaboxContext $context Instance du contexte de l'écran d'affichage.
     * @param MetaboxScreen|null $screen Instance de l'écran d'affichage.
     *
     * @return array
     */
    public function fetchRender(MetaboxContext $context, ?MetaboxScreen $screen = null): array;

    /**
     * Récupération d'un pilote assigné.
     *
     * @param string $alias
     *
     * @return MetaboxDriver|null
     */
    public function get(string $alias): ?MetaboxDriver;

    /**
     * Récupération de l'instance du gestionnaire d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

    /**
     * Récupération d'un contexte assigné.
     *
     * @param string $alias
     *
     * @return MetaboxContext|null
     */
    public function getContext(string $alias): ?MetaboxContext;

    /**
     * Récupération d'un contexte déclaré.
     *
     * @param string $alias
     *
     * @return MetaboxContext|null
     */
    public function getRegisteredContext(string $alias): ?MetaboxContext;

    /**
     * Récupération d'un pilote déclaré.
     *
     * @param string $alias
     *
     * @return MetaboxDriver|null
     */
    public function getRegisteredDriver(string $alias): ?MetaboxDriver;

    /**
     * Récupération d'un écran déclaré.
     *
     * @param string $alias
     *
     * @return MetaboxScreen|null
     */
    public function getRegisteredScreen(string $alias): ?MetaboxScreen;

    /**
     * Récupération de la liste des boîtes de saisie affichée dans un contexte d'affichage.
     *
     * @param string $context
     *
     * @return MetaboxDriver[]|array
     */
    public function getRenderedDrivers(string $context): array;

    /**
     * Récupération d'un écran assigné.
     *
     * @param string $alias
     *
     * @return MetaboxScreen|null
     */
    public function getScreen(string $alias): ?MetaboxScreen;

    /**
     * Déclaration d'un contexte d'affichage.
     *
     * @param string $alias
     * @param MetaboxContext $context
     *
     * @return static
     *
     * @throws Exception
     */
    public function registerContext(string $alias, MetaboxContext $context): Metabox;

    /**
     * Déclaration d'un pilote de boîte de saisie.
     *
     * @param string $alias
     * @param MetaboxDriver $driver
     *
     * @return static
     *
     * @throws Exception
     */
    public function registerDriver(string $alias, MetaboxDriver $driver): Metabox;

    /**
     * Déclaration d'un pilote de boîte de saisie.
     *
     * @param string $alias
     * @param MetaboxScreen $screen
     *
     * @return static
     *
     * @throws Exception
     */
    public function registerScreen(string $alias, MetaboxScreen $screen): Metabox;

    /**
     * Récupération du rendu l'affichage des boîtes de saisies associées à un contexte d'un écran d'affichage.
     *
     * @param string $context Nom de qualification du contexte d'affichage.
     * @param array $args Tableau indexé d'arguments complémentaires.
     *
     * @return string
     */
    public function render(string $context, array $args = []): string;

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
    public function setConfig(array $attrs): Metabox;

    /**
     * Définition du conteneur d'injection de dépendances.
     *
     * @param Container $container
     *
     * @return static
     */
    public function setContainer(Container $container): Metabox;

    /**
     * Déclaration d'un jeu de boîte de saisie boîte de saisie.
     *
     * @param string $screen Nom de qualification de l'écran d'affichage.
     * @param string $context Nom de qualification du contexte de l'écran d'affichage.
     * @param string[][]|array[][]|MetaboxDriver[][] $driversDef Liste des boîtes de saisie.
     *
     * @return static
     *
     * @throws Exception
     */
    public function stack(string $screen, string $context, array $driversDef): Metabox;
}