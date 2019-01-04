<?php

namespace tiFy\Contracts\Routing;

interface Url
{
    /**
     * Récupération de l'url propre. Nettoyée de la liste des arguments à exclure par défaut.
     *
     * @return string
     */
    public function clean();

    /**
     * Liste des arguments à exclure de l'url propre.
     *
     * @return array
     */
    public function cleanArgs();

    /**
     * Récupération de l'url courante. Sans les arguments de requête.
     *
     * @return string
     */
    public function current();

    /**
     * Formatage d'une url au format RFC3986|RFC3987.
     *
     * @param string $format RFC3986|RFC3987.
     * @param string $url Url à formater. Url propre par défaut.
     *
     * @return string
     */
    public function format(string $format = 'RFC3986', string $url = '');

    /**
     * Récupération de l'url courante complète. Arguments de requête inclus.
     *
     * @return string
     */
    public function full();

    /**
     * Récupération de la sous arborescence du chemin de l'url.
     *
     * @return string
     */
    public function rewriteBase();

    /**
     * Récupération de l'url vers la racine.
     *
     * @param string $path Chemin relatif vers une ressource du site.
     *
     * @return string
     */
    public function root($path = '');

    /**
     * Récupération d'une url agrémentée d'une liste d'arguments de requête.
     *
     * @param string[] $args Liste des arguments de requête à inclure.
     * @param string $url Url à agrémenter. Url propre par défaut.
     *
     * @return string
     */
    public function with(array $args, string $url = '');

    /**
     * Récupération d'une url nettoyée d'une liste d'arguments de requête.
     *
     * @param string[] $args Liste des arguments de requête à exclure.
     * @param string $url Url à nettoyer. Url propre par défaut.
     *
     * @return string
     */
    public function without(array $args, string $url = '');
}