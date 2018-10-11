<?php

namespace tiFy\Contracts\Layout;

use tiFy\Contracts\Kernel\ParametersBagInterface;

interface LayoutFactoryInterface extends ParametersBagInterface
{
    /**
     * Récupération de contenu d'affichage de la vue.
     *
     * @return string string.
     */
    public function getContent();


    /**
     * Récupération de l'environnement d'affichage de la vue.
     *
     * @return string admin|front.
     */
    public function getEnv();

    /**
     * Récupération du nom de qualification de la disposition associée.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération du contexte d'affichage.
     *
     * @return bool
     */
    public function isAdmin();

    /**
     * Récupération du controleur d'affichage.
     *
     * @return LayoutItemInterface
     */
    public function layout();

    /**
     * Chargement de la disposition.
     *
     * @return void
     */
    public function load();
}