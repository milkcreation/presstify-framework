<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use Exception;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;

interface FormManager
{
    /**
     * Récupération de l'instance courante.
     *
     * @return static
     *
     * @throws Exception
     */
    public static function instance(): FormManager;

    /**
     * Récupération de la liste des formulaires déclarés.
     *
     * @return FormFactory[]|array
     */
    public function all(): array;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): FormManager;

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
     * Récupération|Définition du formulaire courant.
     *
     * @param string|FormFactory|null $formDefinition Nom de qualification ou instance du formulaire.
     *
     * @return FormFactory|null
     */
    public function current($formDefinition = null): ?FormFactory;

    /**
     * Récupération d'une instance de formulaire associé à son alias de qualification.
     *
     * @param string $alias
     *
     * @return FormFactory|null
     */
    public function get(string $alias): ?FormFactory;

    /**
     * Récupération d'un pilote d'addon déclaré.
     *
     * @param string $alias
     *
     * @return AddonDriver
     */
    public function getAddonDriver(string $alias): ?AddonDriver;

    /**
     * Récupération d'un pilote de bouton déclaré.
     *
     * @param string $alias
     *
     * @return ButtonDriver
     */
    public function getButtonDriver(string $alias): ?ButtonDriver;

    /**
     * Récupération de l'instance du gestionnaire d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

    /**
     * Récupération d'un pilote de champ déclaré.
     *
     * @param string $alias
     *
     * @return FieldDriver
     */
    public function getFieldDriver(string $alias): ?FieldDriver;

    /**
     * Récupération de l'indice de déclaration d'un formulaire.
     *
     * @param string $alias.
     *
     * @return int
     */
    public function getIndex(string $alias): int;

    /**
     * Déclaration d'un formulaire.
     *
     * @param string $alias
     * @param array|FormFactory $formDefinition
     *
     * @return FormFactory
     */
    public function register(string $alias, $formDefinition = []): FormFactory;

    /**
     * Déclaration d'un pilote d'addon.
     *
     * @param string $alias
     * @param AddonDriver|array $addonDefinition
     *
     * @return AddonDriver
     */
    public function registerAddonDriver(string $alias, $addonDefinition = []): AddonDriver;

    /**
     * Déclaration d'un pilote d'addon.
     *
     * @param string $alias
     * @param ButtonDriver|array $buttonDefinition
     *
     * @return ButtonDriver
     */
    public function registerButtonDriver(string $alias, $buttonDefinition = []): ButtonDriver;

    /**
     * Déclaration d'un pilote d'addon.
     *
     * @param string $alias
     * @param FieldDriver|array $fieldDefinition
     *
     * @return FieldDriver
     */
    public function registerFieldDriver(string $alias, $fieldDefinition = []): FieldDriver;

    /**
     * Réinitialisation du formulaire courant.
     *
     * @return static
     */
    public function reset(): FormManager;

    /**
     * Résolution de service fourni par le gestionnaire.
     *
     * @param string $alias
     *
     * @return object|null
     */
    public function resolve(string $alias): ?object;

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
     * Définition d'un pilote d'addon.
     *
     * @param string $alias
     * @param AddonDriver $driver
     *
     * @return $this
     */
    public function setAddonDriver(string $alias, AddonDriver $driver): FormManager;

    /**
     * Définition d'un pilote de bouton.
     *
     * @param string $alias
     * @param ButtonDriver $driver
     *
     * @return $this
     */
    public function setButtonDriver(string $alias, ButtonDriver $driver): FormManager;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): FormManager;

    /**
     * Définition du conteneur d'injection de dépendances.
     *
     * @param Container $container
     *
     * @return static
     */
    public function setContainer(Container $container): FormManager;

    /**
     * Définition d'un pilote de champ.
     *
     * @param string $alias
     * @param FieldDriver $driver
     *
     * @return $this
     */
    public function setFieldDriver(string $alias, FieldDriver $driver): FormManager;

    /**
     * Définition d'un formulaire.
     *
     * @param string $alias
     * @param FormFactory $factory
     *
     * @return $this
     */
    public function setForm(string $alias, FormFactory $factory): FormManager;
}