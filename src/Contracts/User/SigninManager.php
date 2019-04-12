<?php declare(strict_types=1);

namespace tiFy\Contracts\User;

interface SigninManager
{
    /**
     * Récupération de la liste des instance de formulaire d'authentification déclarés.
     *
     * @return SigninFactory[]|array
     */
    public function all(): array;

    /**
     * Récupération d'une instance de formulaire d'authentification déclaré.
     *
     * @param string $name Nom de qualification.
     *
     * @return SigninFactory|null
     */
    public function get(string $name): ?SigninFactory;

    /**
     * Déclaration d'un formulaire d'authentification.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function register(string $name, array $attrs): SigninManager;

    /**
     * Définition d'un formulaire d'authentification.
     *
     * @param SigninFactory $factory Instance du formulaire.
     * @param string|null $name Nom de qualification.
     *
     * @return static
     */
    public function set(SigninFactory $factory, ?string $name = null): SigninManager;
}