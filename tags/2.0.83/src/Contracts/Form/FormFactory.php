<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Kernel\ParamsBag;

interface FormFactory extends FactoryResolver, ParamsBag
{
    /**
     * Résolution de sortie de l'affichage.
     *
     * @return string
     */
    public function __toString();

    /**
     * Initialisation du contrôleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Récupération de la chaîne de sécurisation du formulaire (CSRF).
     *
     * @return string
     */
    public function csrf();

    /**
     * Récupération de l'action du formulaire (url).
     *
     * @return string
     */
    public function getAction();

    /**
     * Récupération de la méthode de soumission du formulaire.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Récupération de l'intitulé de qualification du formulaire.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Vérification d'activation de l'agencement des éléments.
     *
     * @return boolean
     */
    public function hasGrid();

    /**
     * Récupération du numéro d'indice du formulaire.
     *
     * @return null|int
     */
    public function index();

    /**
     * Récupération du nom de qualification du formulaire.
     *
     * @return string
     */
    public function name();

    /**
     * Evénement de déclenchement à l'initialisation du formulaire en tant que formulaire courant.
     *
     * @return void
     */
    public function onSetCurrent();

    /**
     * Evénement de déclenchement à la réinitialisation du formulaire courant du formulaire.
     *
     * @return void
     */
    public function onResetCurrent();

    /**
     * Initialisation (préparation) du champ.
     *
     * @return void
     */
    public function prepare();

    /**
     * Affichage.
     *
     * @return string
     */
    public function render();
}