<?php declare(strict_types=1);

namespace tiFy\Contracts\Metabox;

use tiFy\Contracts\Support\ParamsBag;

interface MetaboxScreen extends ParamsBag
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MetaboxScreen;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function build(): MetaboxScreen;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de la liste des boîtes de saisie associées à l'écran.
     *
     * @return MetaboxDriver[]|array
     */
    public function getDrivers(): array;

    /**
     * Vérifie si la page courante correspond à l'écran.
     *
     * @return boolean
     */
    public function isCurrent(): bool;

    /**
     * Vérifie si la route courante correspond à l'écran.
     *
     * @return boolean
     */
    public function isCurrentRoute(): bool;

    /**
     * Vérifie si la requête courante correspond à l'écran.
     *
     * @return boolean
     */
    public function isCurrentRequest(): bool;

    /**
     * Récupération de l'instance du gestionnaire.
     *
     * @return Metabox|null
     */
    public function metabox(): ?Metabox;

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function parse(): MetaboxScreen;

    /**
     * Définition de l'instance du gestionnaire.
     *
     * @param Metabox $metabox
     *
     * @return static
     */
    public function setMetabox(Metabox $metabox): MetaboxScreen;

    /**
     * Définition du nom de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): MetaboxScreen;
}