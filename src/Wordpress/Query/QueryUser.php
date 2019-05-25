<?php declare(strict_types=1);

namespace tiFy\Wordpress\Query;

use tiFy\Support\ParamsBag;
use tiFy\Wordpress\Contracts\QueryUser as QueryUserContract;
use WP_User;

class QueryUser extends ParamsBag implements QueryUserContract
{
    /**
     * Instance d'utilisateur Wordpress.
     * @var WP_User
     */
    protected $wp_user;

    /**
     * CONSTRUCTEUR
     *
     * @param WP_User $wp_user Instance d'utilisateur Wordpress.
     *
     * @return void
     */
    public function __construct(WP_User $wp_user)
    {
        $this->wp_user = $wp_user;

        $this->set($this->wp_user->to_array())->parse();
    }

    /**
     * @inheritdoc
     */
    public static function createFromGlobal(): QueryUserContract
    {
        return new static(wp_get_current_user());
    }

    /**
     * @inheritdoc
     */
    public static function createFromId($user_id): ?QueryUserContract
    {
        return ($user_id && is_numeric($user_id) && ($wp_user = new WP_User($user_id)) && ($wp_user instanceof WP_User))
            ? new static($wp_user) : null;
    }

    /**
     * @inheritdoc
     */
    public function can(string $capability, array...$args): bool
    {
        return $this->getWpUser()->has_cap($capability, $args);
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return $this->getWpUser()->description;
    }

    /**
     * @inheritdoc
     */
    public function getDisplayName(): string
    {
        return (string)$this->get('display_name', '');
    }

    /**
     * @inheritdoc
     */
    public function getEmail(): string
    {
        return (string)$this->get('user_email', '');
    }

    /**
     * @inheritdoc
     */
    public function getFirstName(): string
    {
        return $this->getWpUser()->first_name;
    }

    /**
     * @inheritdoc
     */
    public function getId(): int
    {
        return intval($this->get('ID', 0));
    }

    /**
     * @inheritdoc
     */
    public function getLastName(): string
    {
        return $this->getWpUser()->last_name;
    }

    /**
     * @inheritdoc
     */
    public function getLogin(): string
    {
        return $this->get('user_login', '');
    }

    /**
     * @inheritdoc
     */
    public function getNicename(): string
    {
        return $this->get('user_nicename', '');
    }

    /**
     * @inheritdoc
     */
    public function getNickname(): string
    {
        return $this->getWpUser()->nickname;
    }

    /**
     * @inheritdoc
     */
    public function getPass(): string
    {
        return $this->get('user_pass', '');
    }

    /**
     * @inheritdoc
     */
    public function getRegistered(): string
    {
        return $this->get('user_registered', '');
    }

    /**
     * @inheritdoc
     */
    public function getRoles(): array
    {
        return $this->getWpUser()->roles;
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return $this->get('user_url', '');
    }

    /**
     * @inheritdoc
     */
    public function getWpUser(): WP_User
    {
        return $this->wp_user;
    }

    /**
     * @inheritdoc
     */
    public function hasRole($role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @inheritdoc
     */
    public function isLoggedIn(): bool
    {
        return wp_get_current_user()->exists();
    }
}