<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Kernel\Http\Request as tiFyRequest;

/**
 * Interface Request
 * @package tiFy\View\Pattern\ListTable\Request
 *
 * @mixin tiFyRequest
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

    /**
     * Définition de l'instance du controleur de motif d'affichage.
     *
     * @param ListTable $pattern Instance du controleur de motif d'affichage.
     *
     * @return $this
     */
    public function setPattern(ListTable $pattern);

    /**
     * {@inheritdoc}
     */
    public function sanitizeUrl($remove_query_args = [], $url = '');
}