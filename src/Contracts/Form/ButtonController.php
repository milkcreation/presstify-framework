<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Kernel\ParamsBagInterface;

interface ButtonController extends ParamsBagInterface
{
    /**
     * Résolution de sortie de l'affichage du contrôleur.
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
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de l'ordre d'affichage.
     *
     * @return int
     */
    public function getPosition();

    /**
     * Affichage.
     *
     * @return string
     */
    public function render();
}