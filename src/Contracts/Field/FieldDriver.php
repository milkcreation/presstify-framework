<?php declare(strict_types=1);

namespace tiFy\Contracts\Field;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\View\Engine as ViewEngine;

interface FieldDriver extends ParamsBag
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
     * @param Field $field
     *
     * @return static
     */
    public function build(string $alias, Field $field): FieldDriver;

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
     * Initialisation du controleur.
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
     * Récupération du gestionnaire de champs.
     *
     * @return Field|null
     */
    public function field(): ?Field;

    /**
     * Récupération de l'identifiant de qualification dans le gestionnaire.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de l'identifiant de qualification.
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
     * Récupération de l'indice dans la requête HTTP de soumission.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de la valeur dans la requête HTTP de soumission.
     *
     * @return mixed|null
     */
    public function getValue();

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function parse(): FieldDriver;

    /**
     * Traitement de l'attribut "class" de la balise HTML.
     *
     * @return static
     */
    public function parseAttrClass(): FieldDriver;

    /**
     * Traitement de l'attribut "id" de la balise HTML.
     *
     * @return static
     */
    public function parseAttrId(): FieldDriver;

    /**
     * Traitement de l'attribut "name" de la balise HTML.
     *
     * @return static
     */
    public function parseAttrName(): FieldDriver;

    /**
     * Traitement de l'attribut "value" de la balise HTML.
     *
     * @return static
     */
    public function parseAttrValue(): FieldDriver;

    /**
     * Traitement de la liste des attributs par défaut.
     *
     * @return static
     */
    public function parseDefaults(): FieldDriver;

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
    public function setId(string $id): FieldDriver;

    /**
     * Définition de l'indice de qualification.
     *
     * @param int $index
     *
     * @return static
     */
    public function setIndex(int $index): FieldDriver;

    /**
     * Définition de l'instance du moteur d'affichage.
     *
     * @param ViewEngine $viewer
     *
     * @return static
     */
    public function setViewEngine(ViewEngine $viewer): FieldDriver;

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