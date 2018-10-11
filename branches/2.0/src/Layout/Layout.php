<?php

namespace tiFy\Layout;

class Layout
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Déclaration d'une disposition
     *
     * @param string $context Contexte d'affichage de la disposition. admin|front.
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return $this
     */
    public function add($context = 'admin', $name, $attrs = [])
    {
        config()->set(
            "layout.{$context}",
            array_merge(
                [$name => $attrs],
                config("layout.{$context}", [])
            )
        );

        return $this;
    }

    /**
     * Récupération du chemin absolu vers le répertoire de stockage des ressources.
     *
     * @param string $path Chemin relatif vers une ressource du répertoire (fichier ou dossier).
     *
     * @return string
     */
    public function resourcesDir($path = '')
    {
        $path = $path ? '/Resources/' . ltrim($path, '/') : '/Resources';

        return file_exists(__DIR__ . $path) ? __DIR__ . $path : '';
    }

    /**
     * Récupération de l'url absolue vers le répertoire de stockage des ressources.
     *
     * @param string $path Chemin relatif vers une ressource du répertoire (fichier ou dossier).
     *
     * @return string
     */
    public function resourcesUrl($path = '')
    {
        $cinfo = class_info($this);
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists($cinfo->getDirname() . $path) ? class_info($this)->getUrl() . $path : '';
    }
}