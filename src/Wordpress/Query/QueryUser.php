<?php declare(strict_types=1);

namespace tiFy\Wordpress\Query;

use Illuminate\Database\Eloquent\{
    Collection as EloquentCollection,
    Model as EloquentModel
};
use tiFy\Contracts\User\RoleFactory;
use tiFy\Support\{Arr, ParamsBag, Proxy\Role};
use tiFy\Wordpress\Contracts\{Database\UserBuilder,
    Query\PaginationQuery as PaginationQueryContract,
    Query\QueryUser as QueryUserContract};
use tiFy\Wordpress\Database\Model\User as UserModel;
use WP_Site, WP_User, WP_User_Query;

/**
 * @property-read int ID
 * @property-read string user_login
 * @property-read string user_pass
 * @property-read string user_nicename
 * @property-read string user_email
 * @property-read string user_url
 * @property-read string user_registered
 * @property-read string user_activation_key
 * @property-read string user_status
 * @property-read string display_name
 */
class QueryUser extends ParamsBag implements QueryUserContract
{
    /**
     * Liste des classes de rappel d'instanciation selon le type de post.
     * @var string[][]|array
     */
    protected static $builtInClasses = [];

    /**
     * Liste des arguments de requête de récupération des éléments par défaut.
     * @var array
     */
    protected static $defaultArgs = [];

    /**
     * Classe de rappel d'instanciation.
     * @var string|null
     */
    protected static $fallbackClass;

    /**
     * Instance de la pagination la dernière requête de récupération d'une liste d'éléments.
     * @var PaginationQueryContract|null
     */
    protected static $pagination;

    /**
     * Nom de qualification ou liste de roles associés.
     * @var string|array
     */
    protected static $role = [];

    /**
     * Liste des sites pour lequels l'utilisateur est habilité.
     * @var WP_Site[]|array
     */
    protected $blogs;

    /**
     * Instance du modèle de base de données associé.
     * @var UserBuilder
     */
    protected $db;

    /**
     * Instance d'utilisateur Wordpress.
     * @var WP_User
     */
    protected $wpUser;

    /**
     * CONSTRUCTEUR
     *
     * @param WP_User|null $wp_user Instance d'utilisateur Wordpress.
     *
     * @return void
     */
    public function __construct(?WP_User $wp_user = null)
    {
        if ($this->wpUser = $wp_user instanceof WP_User ? $wp_user : null) {
            $this->set($this->wpUser->to_array())->parse();
        }
    }

    /**
     * @inheritDoc
     */
    public static function build(object $wp_user): ?QueryUserContract
    {
        if (!$wp_user instanceof WP_User) {
            return null;
        }

        $classes = self::$builtInClasses;
        $role = current($wp_user->roles);

        $class = $classes[$role] ?? (self::$fallbackClass ?: static::class);

        return class_exists($class) ? new $class($wp_user) : new static($wp_user);
    }

    /**
     * @inheritDoc
     */
    public static function create($id = null, ...$args): ?QueryUserContract
    {
        if (is_numeric($id)) {
            return static::createFromId((int)$id);
        } elseif (is_string($id)) {
            return (is_email($id)) ? static::createFromEmail($id) : static::createFromLogin($id);
        } elseif ($id instanceof WP_User) {
            return static::build($id);
        } elseif ($id instanceof QueryUserContract) {
            return static::createFromId($id->getId());
        } elseif (is_null($id)) {
            return static::createFromGlobal();
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function createFromEloquent(EloquentModel $model): ?QueryUserContract
    {
        return static::createFromId((new WP_User((object)$model->getAttributes()))->ID ?: 0);
    }

    /**
     * @inheritDoc
     */
    public static function createFromGlobal(): QueryUserContract
    {
        return new static(wp_get_current_user());
    }

    /**
     * @inheritDoc
     */
    public static function createFromId(int $user_id): ?QueryUserContract
    {
        if ($user_id && ($wp_user = new WP_User($user_id)) && ($wp_user instanceof WP_User)) {
            return static::is($instance = static::build($wp_user)) ? $instance : null;
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function createFromLogin(string $login): ?QueryUserContract
    {
        return (($data = WP_User::get_data_by('login', $login)) && (($wp_user = new WP_User($data)) instanceof WP_User))
            ? static::createFromId($wp_user->ID ?? 0) : null;
    }

    /**
     * @inheritDoc
     */
    public static function createFromEmail(string $email): ?QueryUserContract
    {
        return (($data = WP_User::get_data_by('email', $email)) && (($wp_user = new WP_User($data)) instanceof WP_User))
            ? static::createFromId($wp_user->ID ?? 0) : null;
    }

    /**
     * @inheritDoc
     */
    public static function fetch($query): array
    {
        if (is_array($query)) {
            return static::fetchFromArgs($query);
        } elseif ($query instanceof WP_User_Query) {
            return static::fetchFromWpUserQuery($query);
        } else {
            return [];
        }
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromArgs(array $args = []): array
    {
        return static::fetchFromWpUserQuery(new WP_User_Query(static::parseQueryArgs($args)));
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromEloquent(EloquentCollection $collection): array
    {
        $instances = [];
        foreach ($collection->toArray() as $item) {
            if ($instance = static::createFromId((new WP_User((object)$item))->ID ?: 0)) {
                $instances[] = $instance;
            }
        }

        return $instances;
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromIds(array $ids): array
    {
        return static::fetchFromWpUserQuery(new WP_User_Query(static::parseQueryArgs(['include' => $ids])));
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromWpUserQuery(WP_User_Query $wp_user_query): array
    {
        $users = $wp_user_query->get_results();
        $per_page = $wp_user_query->query_vars['number'] ?: -1;
        $count = count($users);
        $offset = $wp_user_query->query_vars['offset'] ?: 0;

        if ($per_page > 0) {
            $wp_user_query_count = new WP_User_Query(array_merge($wp_user_query->query_vars, [
                'count'  => false,
                'number' => 0,
                'offset' => 0,
                'fields' => 'count',
            ]));

            $total = (int)$wp_user_query_count->get_results();
            $pages = (int)ceil($total / $per_page);
            $page = (int)ceil(($offset + 1) / $per_page);
        } else {
            $pages = 1;
            $page = 1;
            $total = (int)count($users);
        }

        static::pagination()->clear()->set([
            'count'        => $count,
            'current_page' => $page,
            'last_page'    => $pages,
            'per_page'     => $per_page,
            'query_obj'    => $wp_user_query,
            'results'      => [],
            'total'        => $total,
        ]);

        $results = [];
        foreach ($users as $wp_user) {
            $instance = static::createFromId($wp_user->ID);

            if (($role = static::$role) && ($role !== 'any')) {
                if ($instance->roleIn($role)) {
                    $results[] = $instance;
                }
            } else {
                $results[] = $instance;
            }
        }

        static::pagination()->set(compact('results'))->parse();

        return $results;
    }

    /**
     * @inheritDoc
     */
    public static function is($instance): bool
    {
        return $instance instanceof static &&
            ((($role = static::$role) && ($role !== 'any')) ? $instance->roleIn($role) : true);
    }

    /**
     * @inheritDoc
     */
    public static function pagination(): PaginationQueryContract
    {
        if (is_null(static::$pagination)) {
            static::$pagination = new PaginationQuery();
        }

        return static::$pagination;
    }

    /**
     * @inheritDoc
     */
    public static function parseQueryArgs(array $args = []): array
    {
        if ($role = static::$role) {
            $args['role'] = $role;
        } elseif (!isset($args['role_in'])) {
            $args['role_in'] = [];
        }

        return array_merge(static::$defaultArgs, $args);
    }

    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function queryFromArgs(array $args = []): array
    {
        return static::fetchFromArgs($args);
    }

    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function queryFromIds(array $ids): array
    {
        return static::fetchFromIds($ids);
    }

    /**
     * @inheritDoc
     */
    public static function setBuiltInClass(string $role, string $classname): void
    {
        self::$builtInClasses[$role] = $classname;
    }

    /**
     * @inheritDoc
     */
    public static function setDefaultArgs(array $args): void
    {
        self::$defaultArgs = $args;
    }

    /**
     * @inheritDoc
     */
    public static function setFallbackClass(string $classname): void
    {
        self::$fallbackClass = $classname;
    }

    /**
     * @inheritDoc
     */
    public static function setRole(string $role): void
    {
        self::$role = $role;
    }

    /**
     * @inheritDoc
     */
    public function db(): UserBuilder
    {
        if (!$this->db) {
            $this->db = (new UserModel())->find($this->getId());
        }

        return $this->db;
    }

    /**
     * @inheritDoc
     */
    public function can(string $capability, ...$args): bool
    {
        return $this->getWpUser()->has_cap($capability, ...$args);
    }

    /**
     * @inheritDoc
     */
    public function capabilities(): array
    {
        return $this->getWpUser()->allcaps;
    }

    /**
     * @inheritDoc
     */
    public function getBlogs($all = false): iterable
    {
        if (is_null($this->blogs)) {
            $this->blogs = get_blogs_of_user($this->getId(), $all);

            array_walk($this->blogs, function (&$site) {
                $site = WP_Site::get_instance($site->userblog_id);
            });
        }

        return $this->blogs;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->getWpUser()->description ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getDisplayName(): string
    {
        return (string)$this->get('display_name', '');
    }

    /**
     * @inheritDoc
     */
    public function getEditUrl(): string
    {
        return get_edit_user_link($this->getId());
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return (string)$this->get('user_email', '');
    }

    /**
     * @inheritDoc
     */
    public function getFirstName(): string
    {
        return $this->getWpUser()->first_name ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return intval($this->get('ID', 0));
    }

    /**
     * @inheritDoc
     */
    public function getLastName(): string
    {
        return $this->getWpUser()->last_name ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getLogin(): string
    {
        return $this->get('user_login', '');
    }

    /**
     * @inheritDoc
     */
    public function getMeta(string $meta_key, bool $single = false, $default = null)
    {
        return get_user_meta($this->getId(), $meta_key, $single) ?: $default;
    }

    /**
     * @inheritDoc
     */
    public function getMetaMulti(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, false, $default);
    }

    /**
     * @inheritDoc
     */
    public function getMetaSingle(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, true, $default);
    }

    /**
     * @inheritDoc
     */
    public function getNicename(): string
    {
        return $this->get('user_nicename', '');
    }

    /**
     * @inheritDoc
     */
    public function getNickname(): string
    {
        return $this->getWpUser()->nickname ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $option_name, $default = null)
    {
        return get_user_option($option_name, $this->getId()) ?: $default;
    }

    /**
     * @inheritDoc
     */
    public function getPass(): string
    {
        return $this->get('user_pass', '');
    }

    /**
     * @inheritDoc
     */
    public function getRegistered(): string
    {
        return $this->get('user_registered', '');
    }

    /**
     * {@inheritDoc}
     *
     * @return RoleFactory[]|array
     */
    public function getRoles(): array
    {
        $roles = $this->getWpUser()->roles;

        $_roles = [];
        array_walk($roles, function ($role) use (&$_roles) {
            if ($_role = Role::get($role)) {
                $_roles[$role] = $_role;
            }
        });

        return $_roles;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->get('user_url', '');
    }

    /**
     * @inheritDoc
     */
    public function getWpUser(): WP_User
    {
        return $this->wpUser;
    }

    /**
     * @inheritDoc
     */
    public function hasRole(string $role): bool
    {
        return $this->roleIn([$role]);
    }

    /**
     * @inheritDoc
     */
    public function isLoggedIn(): bool
    {
        return wp_get_current_user()->exists();
    }

    /**
     * @inheritDoc
     */
    public function roleIn($roles): bool
    {
        return !!array_intersect(array_keys($this->getRoles()), Arr::wrap($roles));
    }

    /**
     * @inheritDoc
     */
    public function save($userdata): void
    {
        $p = ParamsBag::createFromAttrs($userdata);
        $columns = $this->db()->getConnection()->getSchemaBuilder()->getColumnListing($this->db()->getTable());

        $update = [];
        foreach ($columns as $col) {
            if ($p->has($col)) {
                $update[$col] = $p->get($col);
                if ($col === 'user_pass') {
                    $update[$col] = wp_hash_password($update[$col]);
                }
            }
        }

        $keys = [
            'first_name',
            'last_name',
            'nickname',
            'description',
            'rich_editing',
            'syntax_highlighting',
            'comment_shortcuts',
            'admin_color',
            'use_ssl',
            'show_admin_bar_front',
            'locale',
        ];
        foreach ($keys as $key) {
            if ($value = $p->pull($key)) {
                $p->set("meta.{$key}", $value);
            }
        }

        if ($update) {
            $this->db()->where(['ID' => $this->getId()])->update($update);
        }

        if ($p->has('meta')) {
            $this->saveMeta($p->get('meta'));
        }
    }

    /**
     * @inheritDoc
     */
    public function saveMeta($key, $value = null): void
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $k => $v) {
            $this->db()->saveMeta($k, $v);
        }
    }
}