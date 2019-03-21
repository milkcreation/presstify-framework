<?php

namespace tiFy\Contracts\Template;

interface TemplateManager
{
    /**
     * Récupération d'un motif d'affichage.
     *
     * @param string $name Nom de qualification du motif.
     *
     * @return null|TemplateFactory
     */
    public function get($name);

    /**
     * Déclaration d'un motif d'affichage.
     *
     * @param string $name Nom de qualification de la disposition.
     * @param array $attrs Liste des attributs de configuration de la disposition.
     *
     * @return TemplateFactory
     */
    public function register($name, $attrs = []);

    /**
     * Récupération du chemin absolu vers le répertoire de stockage des ressources.
     *
     * @param string $path Chemin relatif vers une ressource du répertoire (fichier ou dossier).
     *
     * @return string
     */
    public function resourcesDir($path = '');

    /**
     * Récupération de l'url absolue vers le répertoire de stockage des ressources.
     *
     * @param string $path Chemin relatif vers une ressource du répertoire (fichier ou dossier).
     *
     * @return string
     */
    public function resourcesUrl($path = '');

    /**
     * Définition d'un motif d'affichage.
     *
     * @param string $name Nom de qualification de la disposition.
     * @param TemplateFactory $pattern Motif d'affichage.
     *
     * @return TemplateFactory
     */
    public function set($name, TemplateFactory $pattern);
}