<?php

namespace tiFy\Template\Templates\ListTable\Contracts;

/**
 * Interface Request
 * @package tiFy\Template\Templates\ListTable\Request
 *
 * @mixin \tiFy\Template\Templates\BaseRequest
 */
interface Request
{
    /**
     * Récupération du nombre d'éléments par page.
     *
     * @return int
     */
    public function getPerPage();

    /**
     * Récupération du numéro de la page courante.
     *
     * @return int
     */
    public function getPagenum();

    /**
     * Récupération du la liste des arguments de requête.
     *
     * @return int
     */
    public function getQueryArgs();

    /**
     * Vérifie si la requête HTTP courante répond à une recherche.
     *
     * @return boolean
     */
    public function searchExists();

    /**
     * Récupération du terme de recherche.
     *
     * @return string
     */
    public function searchTerm();
}