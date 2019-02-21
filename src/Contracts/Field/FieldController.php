<?php

namespace tiFy\Contracts\Field;

use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;

interface FieldController extends ParamsBag
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
     * Récupération de l'attribut de configuration de la qualification de soumission du champ "name".
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de l'attribut de configuration de la valeur initiale de soumission du champ "value".
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Vérifie si une variable peut être appelée en tant que fonction.
     *
     * @param mixed $var
     *
     * @return bool
     */
    public function isCallable($var);

    /**
     * Vérification de correspondance entre la valeur de coche et celle du champ.
     *
     * @return bool
     */
    public function isChecked();

    /**
     * Traitement de la liste des attributs par défaut.
     *
     * @return void
     */
    public function parseDefaults();

    /**
     * Traitement de l'attribut de configuration de la clé d'indexe de soumission du champ "name".
     *
     * @return void
     */
    public function parseName();

    /**
     * Traitement de l'attribut de configuration de la valeur de soumission du champ "value".
     *
     * @return void
     */
    public function parseValue();

    /**
     * Récupération d'un instance du controleur de liste des gabarits d'affichage ou d'un gabarit d'affichage.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'instance du controleur de liste.}
     * {@internal Sinon récupére l'instance du gabarit d'affichage et passe les variables en argument.}
     *
     * @param null|string view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewController|ViewEngine
     */
    public function viewer($view = null, $data = []);
}