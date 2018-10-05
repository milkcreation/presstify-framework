<?php

namespace tiFy\Contracts\Partial;

use tiFy\Contracts\Kernel\ParametersBagInterface;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Contracts\Views\ViewInterface;

interface PartialItemInterface extends ParametersBagInterface
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Affichage du contenu placé après le champ
     *
     * @return void
     */
    public function after();

    /**
     * Affichage de la liste des attributs de balise.
     *
     * @return string
     */
    public function attrs();

    /**
     * Affichage du contenu placé avant le champ
     *
     * @return void
     */
    public function before();

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Affichage du contenu de la balise champ.
     *
     * @return void
     */
    public function content();

    /**
     * Affichage.
     *
     * @return string
     */
    public function display();

    /**
     * Mise en file des scripts CSS et JS utilisés pour l'affichage.
     *
     * @return $this
     */
    public function enqueue_scripts();

    /**
     * Traitement d'une liste d'attributs HTML.
     *
     * @param array $attrs Liste des attributs HTML.
     * @param bool $linearized Activation de la linéarisation.
     *
     * @return string
     */
    public function getHtmlAttrs($attrs = [], $linearized = true);

    /**
     * Récupération de l'identifiant de qualification du controleur.
     *
     * @return string
     */
    public function getId();

    /**
     * Récupération de l'indice de la classe courante.
     *
     * @return int
     */
    public function getIndex();

    /**
     * Vérifie si une variable peut être appelée en tant que fonction.
     *
     * @return bool
     */
    public function isCallable($var);

    /**
     * Récupération de la vue.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'intance du controleur principal.}
     * {@internal Sinon récupére le gabarit d'affichage et passe les variables en argument.}
     *
     * @param null|string view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewsInterface|ViewInterface
     */
    public function viewer($view = null, $data = []);
}