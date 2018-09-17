<?php

namespace tiFy\Contracts\Metabox;

use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\Contracts\Kernel\ParametersBagInterface;

interface MetaboxItemInterface extends ParametersBagInterface
{
    /**
     * Récupération de la liste des variables passées en argument.
     *
     * @return array
     */
    public function getArgs();

    /**
     * Récupération de l'indice de qualification.
     *
     * @return integer
     */
    public function getIndex();

    /**
     * Récupération du contenu de l'affichage.
     *
     * @return string
     */
    public function getContent();

    /**
     * Récupération du contexte d'affichage.
     *
     * @return string
     */
    public function getContext();

    /**
     * Récupération de la qualification de la page d'affichage.
     *
     * @return null|string|\WP_Screen
     */
    public function getDisplayPage();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération du nom de qualification du parent associé.
     *
     * @return string
     */
    public function getParent();

    /**
     * Récupération de l'ordre d'affichage.
     *
     * @return integer
     */
    public function getPosition();

    /**
     * Récupération de l'instance de l'écran d'affichage.
     *
     * @return WpScreenInterface
     */
    public function getScreen();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Vérification de l'activation.
     *
     * @return boolean
     */
    public function isActive();

    /**
     * Chargement.
     *
     * @param WpScreenInterface $screen Instance de l'écran courant.
     *
     * @return void
     */
    public function load(WpScreenInterface $current_screen);
}