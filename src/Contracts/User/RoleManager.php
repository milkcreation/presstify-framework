<?php declare(strict_types=1);

namespace tiFy\Contracts\User;

/**
 * Interface RoleManager
 * @package tiFy\User\Role
 *
 * @see https://codex.wordpress.org/Roles_and_Capabilities
 */
interface RoleManager
{
    /**
     * Récupération d'une instance de rôle déclaré.
     *
     * @param string $name Nom de qualification du rôle.
     *
     * @return null|RoleFactory
     */
    public function get(string $name): ?RoleFactory;

    /**
     * Définition d'un rôle.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function register(string $name, array $attrs): RoleManager;

    /**
     * Déclaration d'un rôle.
     *
     * @param RoleFactory $factory Instance du rôle.
     * @param string|null $name Nom de qualification du rôle.
     *
     * @return static
     */
    public function set(RoleFactory $factory, ?string $name = null): RoleManager;
}