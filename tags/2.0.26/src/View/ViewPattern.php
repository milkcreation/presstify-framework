<?php

namespace tiFy\View;

use tiFy\Contracts\View\ViewPatternController;

class ViewPattern
{
    /**
     * Liste des éléments déclarés.
     * @var array
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        foreach (config('view.pattern', []) as $name => $attrs) :
            $this->register($name, $attrs);
        endforeach;
    }

    /**
     * Déclaration d'un motif d'affichage.
     *
     * @param string $name Nom de qualification de la disposition.
     * @param array $attrs Liste des attributs de configuration de la disposition.
     *
     * @return ViewPatternController
     */
    public function register($name, $attrs = [])
    {
        return $this->set($name, $this->items[$name] ?? app()->get('view.pattern.controller', [$name, $attrs]));
    }

    /**
     * Définition d'un motif d'affichage.
     *
     * @param string $name Nom de qualification de la disposition.
     * @param ViewPatternController $pattern Motif d'affichage.
     *
     * @return ViewPatternController
     */
    public function set($name, ViewPatternController $pattern)
    {
        return $this->items[$name] = $pattern;
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