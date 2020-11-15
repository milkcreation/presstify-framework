<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\View\Engine as ViewEngine;

interface PartialDriver extends ParamsBag
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Construction du pilote.
     *
     * @param string $alias
     * @param Partial $partial
     *
     * @return static
     */
    public function build(string $alias, Partial $partial): PartialDriver;

    /**
     * Post-affichage.
     *
     * @return void
     */
    public function after(): void;

    /**
     * Affichage de la liste des attributs de balise.
     *
     * @return void
     */
    public function attrs(): void;

    /**
     * Pré-affichage.
     *
     * @return void
     */
    public function before(): void;

    /**
     * Initialisation.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Affichage du contenu.
     *
     * @return void
     */
    public function content(): void;

    /**
     * Récupération de l'identifiant de qualification dans le gestionnaire.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de l'identifiant de qualification local.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Récupération de l'indice de qualification dans le gestionnaire.
     *
     * @return int
     */
    public function getIndex(): int;

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function parse(): PartialDriver;

    /**
     * Traitement de l'attribut "class" de la balise HTML.
     *
     * @return static
     */
    public function parseAttrClass(): PartialDriver;

    /**
     * Traitement de l'attribut "id" de la balise HTML.
     *
     * @return static
     */
    public function parseAttrId(): PartialDriver;

    /**
     * Traitement de la liste des attributs par défaut.
     *
     * @return static
     */
    public function parseDefaults(): PartialDriver;

    /**
     * Traitement des attributs de configuration du pilote d'affichage.
     *
     * @return $this
     */
    public function parseViewer(): PartialDriver;

    /**
     * Récupération du gestionnaire.
     *
     * @return Partial|null
     */
    public function partial(): ?Partial;

    /**
     * Affichage.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition de la liste des paramètres par défaut.
     *
     * @param array $defaults
     *
     * @return void
     */
    public static function setDefaults(array $defaults = []): void;

    /**
     * Définition de l'identifiant de qualification.
     *
     * @param string $id
     *
     * @return static
     */
    public function setId(string $id): PartialDriver;

    /**
     * Définition de l'indice de qualification.
     *
     * @param int $index
     *
     * @return static
     */
    public function setIndex(int $index): PartialDriver;

    /**
     * Définition de l'instance du moteur d'affichage.
     *
     * @param ViewEngine $viewer
     *
     * @return static
     */
    public function setViewEngine(ViewEngine $viewer): PartialDriver;

    /**
     * Instance du gestionnaire de gabarits d'affichage ou rendu du gabarit d'affichage.
     *
     * @param string|null view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewEngine|string
     */
    public function view(?string $view = null, array $data = []);

    /**
     * Chemin absolu du répertoire des gabarits d'affichage.
     *
     * @return string
     */
    public function viewDirectory(): string;
}