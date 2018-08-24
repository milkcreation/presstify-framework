<?php

namespace tiFy\App\Layout\Request;

interface RequestInterface
{
    /**
     * Récupération de la liste des arguments de requête.
     *
     * @return array
     */
    public function getQueryArgs();

    /**
     * Nettoyage et récupération d'une url.
     *
     * @param string[] Liste des arguments de l'url à supprimer.
     * @param string $url Url à nettoyer. Url courant par défault.
     *
     * @return string
     */
    public function sanitizeUrl($remove_query_args = [], $url = '');
}