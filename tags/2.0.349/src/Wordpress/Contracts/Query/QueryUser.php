<?php declare(strict_types=1);

namespace tiFy\Wordpress\Contracts\Query;

use Illuminate\Database\Eloquent\{
    Collection as EloquentCollection,
    Model as EloquentModel
};
use tiFy\Contracts\{Support\ParamsBag, User\RoleFactory};
use tiFy\Wordpress\Contracts\Database\UserBuilder;
use WP_Site, WP_User, WP_User_Query;

interface QueryUser extends ParamsBag
{
    /**
     * Création d'une instance basée sur un objet post Wordpress et selon la cartographie des classes de rappel.
     *
     * @param WP_User $wp_user
     *
     * @return static
     */
    public static function build(object $wp_user): ?QueryUser;

    /**
     * Création d'un instance basée sur un argument de qualification.
     *
     * @param int|string|WP_User $id
     * @param array ...$args Liste des arguments de qualification complémentaires.
     *
     * @return static|null
     */
    public static function create($id = null, ...$args): ?QueryUser;

    /**
     * Récupération d'une instance basée sur un modèle Laravel.
     *
     * @param EloquentModel $model
     *
     * @return static|null
     */
    public static function createFromEloquent(EloquentModel $model): ?QueryUser;

    /**
     * Création d'un instance de la classe basée sur l'utilisateur courant.
     *
     * @return static
     */
    public static function createFromGlobal(): QueryUser;

    /**
     * Création d'une instance de la classe basée sur un identifiant de qualification existant.
     *
     * @param int $user_id
     *
     * @return static|null
     */
    public static function createFromId(int $user_id): ?QueryUser;

    /**
     * Création d'une instance de la classe basée sur un identifiant de connexion existant.
     *
     * @param string $login
     *
     * @return static|null
     */
    public static function createFromLogin(string $login): ?QueryUser;

    /**
     * Création d'une instance de la classe basée sur un email utilisateur existant.
     *
     * @param string $email
     *
     * @return static|null
     */
    public static function createFromEmail(string $email): ?QueryUser;

    /**
     * Récupération d'une liste des instances des termes courants|selon une requête WP_User_Query|selon une liste d'arguments.
     *
     * @param WP_User_Query|array $query
     *
     * @return QueryPost[]|array
     */
    public static function fetch($query): array;

    /**
     * Récupération d'une liste d'instances basée sur des arguments de requête de récupération des éléments.
     * @see https://developer.wordpress.org/reference/classes/wp_user_query/
     *
     * @param array $args Liste des arguments de la requête récupération des éléments.
     *
     * @return array
     */
    public static function fetchFromArgs(array $args = []): array;

    /**
     * Récupération d'une liste d'instances basée sur un resultat de requête en base de données.
     *
     * @param EloquentCollection $collection
     *
     * @return array
     */
    public static function fetchFromEloquent(EloquentCollection $collection): array;

    /**
     * Récupération d'une liste d'instances basée sur des identifiants de qualification de termes.
     * @see https://developer.wordpress.org/reference/classes/wp_user_query/
     *
     * @param int[] $ids Liste des identifiants de qualification.
     *
     * @return array
     */
    public static function fetchFromIds(array $ids): array;

    /**
     * Récupération d'une liste d'instances basée sur une instance de classe WP_Term_Query.
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/
     *
     * @param WP_User_Query $wp_user_query
     *
     * @return array
     */
    public static function fetchFromWpUserQuery(WP_User_Query $wp_user_query): array;

    /**
     * Vérification d'intégrité d'une instance.
     *
     * @param QueryUser|mixed $instance
     *
     * @return bool
     */
    public static function is($instance): bool;

    /**
     * Récupération de l'instance de pagination de la dernière requête de récupération d'une liste d'éléments.
     *
     * @return PaginationQuery
     */
    public static function pagination(): PaginationQuery;

    /**
     * Traitement d'arguments de requête de récupération des éléments.
     *
     * @param array $args Liste des arguments de la requête récupération des éléments.
     *
     * @return array
     */
    public static function parseQueryArgs(array $args = []): array;

    /**
     * @param array $args Liste des arguments de la requête récupération des éléments.
     *
     * @return array
     *
     * @deprecated
     */
    public static function queryFromArgs(array $args = []): array;

    /**
     * @param int[] $ids Liste des identifiants de qualification.
     *
     * @return array
     *
     * @deprecated
     */
    public static function queryFromIds(array $ids): array;

    /**
     * Définition d'une classe de rappel d'instanciation selon un type de post.
     *
     * @param string $role Nom de qualification du role associé.
     * @param string $classname Nom de qualification de la classe.
     *
     * @return void
     */
    public static function setBuiltInClass(string $role, string $classname): void;

    /**
     * Définition de la liste des arguments de requête de récupération des éléments.
     *
     * @param array $args
     *
     * @return void
     */
    public static function setDefaultArgs(array $args): void;

    /**
     * Définition de la classe de rappel par défaut.
     *
     * @param string $classname Nom de qualification de la classe.
     *
     * @return void
     */
    public static function setFallbackClass(string $classname): void;

    /**
     * Définition du rôle ou une liste de rôles associés.
     *
     * @param string $role
     *
     * @return void
     */
    public static function setRole(string $role): void;

    /**
     * Récupération de l'instance du modèle de base de donnée associé.
     *
     * @return UserBuilder
     */
    public function db(): UserBuilder;

    /**
     * Vérification des habilitations.
     * @see WP_User::has_cap()
     * @see map_meta_cap()
     *
     * @param string $capability Nom de qalification de l'habiltation.
     * @param array $args Liste de paramètres dynamique passé en arguments.
     *
     * @return boolean
     */
    public function can(string $capability, ...$args): bool;

    /**
     * Récupération de la liste des habilitations associées.
     *
     * @return array
     */
    public function capabilities(): array;

    /**
     * Récupération de la liste des sites pour lequels l'utilisateur est habilité.
     *
     * @param boolean $all Tous les sites, si actif. Par défaut tous hormis deleted|archived|spam.
     *
     * @return WP_Site[]
     */
    public function getBlogs(bool $all = false): iterable;

    /**
     * Récupération des renseignements biographiques.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Récupération du nom d'affichage publique.
     *
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Récupération de l'url d'édition de l'utilisateur.
     *
     * @return string
     */
    public function getEditUrl(): string;

    /**
     * Récupération de l'email.
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Récupération du prénom.
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Récupération de l'identifiant de qualification Wordpress de l'utilisateur.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Récupération du nom de famille.
     *
     * @return string
     */
    public function getLastName(): string;

    /**
     * Récupération de l'identifiant de connection de l'utilisateur.
     *
     * @return string
     */
    public function getLogin(): string;

    /**
     * Récupération d'une metadonnée.
     *
     * @param string $meta_key Clé d'indexe de la metadonnée à récupérer
     * @param bool $single Type de metadonnés. single (true)|multiple (false). false par défaut.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getMeta(string $meta_key, bool $single = false, $default = null);

    /**
     * Récupération d'une metadonnée de type multiple.
     *
     * @param string $meta_key Clé d'indexe de la metadonnée à récupérer
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getMetaMulti(string $meta_key, $default = null);

    /**
     * Récupération d'une metadonnée de type simple.
     *
     * @param string $meta_key Clé d'indexe de la metadonnée à récupérer
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getMetaSingle(string $meta_key, $default = null);

    /**
     * Récupération du surnom.
     *
     * @return string
     */
    public function getNicename(): string;

    /**
     * Récupération du pseudonyme.
     *
     * @return string
     */
    public function getNickname(): string;

    /**
     * Récupération d'une option de site.
     *
     * @param string $option_name Clé d'indexe de l'option à récupérer
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getOption(string $option_name, $default = null);

    /**
     * Récupération du mot de passe encrypté.
     *
     * @return string
     */
    public function getPass(): string;

    /**
     * Récupération de la date de création du compte utilisateur.
     *
     * @return string
     */
    public function getRegistered(): string;

    /**
     * Récupération de la liste des roles.
     *
     * @return RoleFactory[]|array
     */
    public function getRoles(): array;

    /**
     * Récupération de l'url du site internet associé à l'utilisateur.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Récupération de l'objet utilisateur Wordpress associé.
     *
     * @return WP_User
     */
    public function getWpUser(): WP_User;

    /**
     * Vérification de l'appartenance à un role.
     *
     * @param string $role Identifiant de qualification du rôle.
     *
     * @return boolean
     */
    public function hasRole(string $role): bool;

    /**
     * Vérifie si l'utilisateur est connecté.
     *
     * @return boolean
     */
    public function isLoggedIn(): bool;

    /**
     * Vérification d'appartenance selon une liste de rôles fournis.
     *
     * @param string|string[] $roles Liste des rôles parmis lequels vérifier.
     *
     * @return boolean
     */
    public function roleIn($roles): bool;

    /**
     * Sauvegarde des données de l'utilisateur en base.
     *
     * @param array $userdata Liste des données à enregistrer
     *
     * @return void
     */
    public function save($userdata): void;

    /**
     * Sauvegarde (Ajout ou mise à jour) de metadonnées de l'utilisateur en base.
     *
     * @param string|array $key Indice de métadonnées ou tableau associatif clé/valeur.
     * @param mixed $value Valeur de la métadonnées si key est un indice.
     *
     * @return void
     */
    public function saveMeta($key, $value = null): void;
}