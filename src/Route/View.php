<?php

namespace tiFy\Route;

use tiFy\Apps\AppController;

class View extends AppController
{
    /**
     * Chemin vers le template d'affichage.
     * @var string
     */
    private $path = '';

    /**
     * Liste des arguments passés à la vue.
     * @var array
     */
    private $args = [];

    /**
     * Traitement de la classe comme une chaîne de caractère.
     * @internal Affichage du rendu
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Déclaration.
     *
     * @return self
     */
    public static function register($path, $args = [])
    {
        return (new self)
            ->path($path)
            ->with($args);
    }

    /**
     * Définition du chemin.
     *
     * @param $path
     *
     * @return $this
     */
    private function path($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Définition du chemin.
     *
     * @param $path
     *
     * @return $this
     */
    private function with($args)
    {
        $this->args = array_merge(
            $args,
            $this->args
        );

        return $this;
    }

    /**
     * Affichage du gabarit.
     *
     * @return string
     */
    public function render()
    {
        return $this->appTemplateRender($this->path, $this->args);
    }
}