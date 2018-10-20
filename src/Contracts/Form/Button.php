<?php

namespace tiFy\Contracts\Form;

interface Button
{
    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de la liste des attributs de balise HTML.
     *
     * @return array
     */
    public function getHtmlAttrs();

    /**
     * Affichage de la liste des attributs de balise HTML.
     *
     * @return string
     */
    public function displayHtmlAttrs();

    /**
     * Affichage du bouton.
     *
     * @return string
     */
    public function display();

    /**
     * Rendu d'affichage du bouton.
     *
     * @return string
     */
    public function render();
}