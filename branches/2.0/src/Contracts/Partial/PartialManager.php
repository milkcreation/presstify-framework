<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

use BadMethodCallException;

interface PartialManager
{
    /**
     * Récupération statique d'un élément.
     *
     * @param string $name Nom de qualification.
     * @param array $arguments Liste des variables passées en arguments.
     *
     * @return PartialFactory|null
     *
     * @throws BadMethodCallException
     */
    public static function __callStatic(string $name, ?array $arguments): ?PartialFactory;

    /**
     * Récupération du nom de qualification de la classe d'un controleur de gabarit.
     *
     * @return string|null
     */
    public function classname(PartialFactory $factory): ?string;

    /**
     * Récupération de l'instance d'un élément déclaré.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param string|array|null $id Nom de qualification ou Liste des attributs de configuration.
     * @param array|null $attrs Liste des attributs de configuration.
     *
     * @return PartialFactory|null
     */
    public function get(string $name, $id = null, ?array $attrs = null): ?PartialFactory;

    /**
     * Récupération de l'index d'un contrôleur d'affichage déclaré.
     *
     * @param PartialFactory $factory Instance du contrôleur de champ.
     *
     * @return int
     */
    public function index(PartialFactory $factory): int;

    /**
     * Déclaration d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification d"appel de l'élément.
     * @param PartialFactory $factory Nom de qualification du controleur.
     *
     * @return boolean
     */
    public function register(string $name, PartialFactory $factory);

    /**
     * Récupération du chemin absolu vers le répertoire des ressources.
     *
     * @param string $path Chemin relatif du sous-repertoire.
     *
     * @return string
     */
    public function resourcesDir(?string $path = null): string;

    /**
     * Récupération de l'url absolue vers le répertoire des ressources.
     *
     * @param string $path Chemin relatif du sous-repertoire.
     *
     * @return string
     */
    public function resourcesUrl(?string $path = null): string;
}