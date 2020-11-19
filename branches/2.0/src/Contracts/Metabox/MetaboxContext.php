<?php declare(strict_types=1);

namespace tiFy\Contracts\Metabox;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\View\PlatesEngine;

interface MetaboxContext extends ParamsBag
{
    /**
     * Résolution de sortie de la classe sous forme de chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MetaboxContext;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function build(): MetaboxContext;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

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
    public function parse(): MetaboxContext;

    /**
     * Récupération du rendu d'affichage du contexte.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition de l'instance du gestionnaire.
     *
     * @param Metabox $metabox
     *
     * @return static
     */
    public function setMetabox(Metabox $metabox): MetaboxContext;

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): MetaboxContext;

    /**
     * Récupération d'un instance du controleur de liste des gabarits d'affichage ou d'un gabarit d'affichage.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'instance du controleur de liste.}
     * {@internal Sinon récupére l'instance du gabarit d'affichage et passe les variables en argument.}
     *
     * @param string|null view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return PlatesEngine|string
     */
    public function view(?string $view = null, array $data = []);
}