<?php

namespace tiFy\AdminView;

interface AdminViewControllerInterface
{
    /**
     * Récupération du nom de qualification d'accroche de la page d'affichage de l'interface.
     *
     * @return string
     */
    public function getHookname();

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getName();
}