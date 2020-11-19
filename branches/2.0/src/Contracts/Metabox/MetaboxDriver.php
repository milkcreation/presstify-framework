<?php declare(strict_types=1);

namespace tiFy\Contracts\Metabox;

use Closure, Exception;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\View\View as ViewEngine;

interface MetaboxDriver extends ParamsBag
{
    /**
     * Résolution de sortie de la classe sous forme d'une chîane de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MetaboxDriver;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function build(): MetaboxDriver;

    /**
     * Liste des paramètres par défaut.
     *
     * @return array
     */
    public function defaultParams(): array;

    /**
     * Récupération de l'identifiant de qualification dans le gestionnaire.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de l'instance du contexte d'affichage associé.
     *
     * @return MetaboxContext|null
     */
    public function getContext(): ?MetaboxContext;

    /**
     * Récupération de l'instance de l'écran d'affichage associé.
     *
     * @return MetaboxScreen|null
     */
    public function getScreen(): ?MetaboxScreen;

    /**
     * Traitement
     *
     * @param array $args Liste des arguments de traitement
     *
     * @return static
     */
    public function handle(array $args = []): MetaboxDriver;

    /**
     * Récupération de l'instance du gestionnaire.
     *
     * @return Metabox|null
     */
    public function metabox(): ?Metabox;

    /**
     * Récupération du nom de qualification dans la requête d'enregistrement des données.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Récupération de l'instance des paramètres|récupération d'un paramètre|Définition de paramètres.
     *
     * @param string|array|null $key Clé d'indice du paramètres. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function params($key = null, $default = null);

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function parse(): ?MetaboxDriver;

    /**
     * Récupération de l'affichage du contenu de la boîte de saisie.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): MetaboxDriver;

    /**
     * Définition de l'instance du contexte d'affichage.
     *
     * @param string $alias
     *
     * @return static
     *
     * @throws Exception
     */
    public function setContext(string $alias): MetaboxDriver;

    /**
     * Définition d'une fonction de traitement.
     *
     * @param Closure $func
     *
     * @return static
     */
    public function setHandler(Closure $func): MetaboxDriver;

    /**
     * Définition de l'instance du gestionnaire.
     *
     * @param Metabox $metabox
     *
     * @return static
     */
    public function setMetabox(Metabox $metabox): MetaboxDriver;

    /**
     * Définition de l'instance de l'écran d'affichage.
     *
     * @param string $alias
     *
     * @return static
     *
     * @throws Exception
     */
    public function setScreen(string $alias): MetaboxDriver;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function title(): string;

    /**
     * Récupération de la valeur courante.
     *
     * @param string|null $key Indice de qualification de la valeur. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function value(?string $key = null, $default = null);

    /**
     * Récupération d'un instance du controleur de liste des gabarits d'affichage ou d'un gabarit d'affichage.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'instance du controleur de liste.}
     * {@internal Sinon récupére l'instance du gabarit d'affichage et passe les variables en argument.}
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