<?php declare(strict_types=1);

namespace tiFy\Contracts\Metabox;

use tiFy\Contracts\{
    Support\ParamsBag,
    View\ViewController,
    View\ViewEngine
};

interface MetaboxDriver extends ParamsBag
{
    /**
     * Initialisation de la boîte de saisie.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération de l'affichage du contenu de la boîte de saisie.
     *
     * @return string
     */
    public function content(): string;

    /**
     * Récupération de l'instance du contexte d'affichage associé.
     *
     * @return MetaboxContext|null
     */
    public function context(): ?MetaboxContext;

    /**
     * Liste des paramètres par défaut.
     *
     * @return string
     */
    public function defaultParams(): array;

    /**
     * Récupération de l'instance du gestionnaire.
     *
     * @return MetaboxManager|null
     */
    public function manager(): ?MetaboxManager;

    /**
     * Récupération de l'instance des paramètres|récupération d'un paramètre|Définition de paramètres.
     *
     * @param string|array|null $key Clé d'indice du paramètres. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return ParamsBag|mixed
     */
    public function params($key = null, $default = null);

    /**
     * Récupération de l'instance de l'écran d'affichage associé.
     *
     * @return MetaboxScreen|null
     */
    public function screen(): ?MetaboxScreen;

    /**
     * Définition de l'instance du contexte d'affichage.
     *
     * @param string $context
     *
     * @return static
     */
    public function setContext(string $context): MetaboxDriver;

    /**
     * Définition de l'instance du gestionnaire.
     *
     * @param MetaboxManager $manager
     *
     * @return static
     */
    public function setManager(MetaboxManager $manager): MetaboxDriver;

    /**
     * Définition de l'instance de l'écran d'affichage.
     *
     * @param string $name Nom de qualification
     *
     * @return static
     */
    public function setScreen(string $screen): MetaboxDriver;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function title(): string;

    /**
     * Récupération d'un instance du controleur de liste des gabarits d'affichage ou d'un gabarit d'affichage.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'instance du controleur de liste.}
     * {@internal Sinon récupére l'instance du gabarit d'affichage et passe les variables en argument.}
     *
     * @param string|null view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewController|ViewEngine
     */
    public function viewer(?string $view = null, array $data = []);
}