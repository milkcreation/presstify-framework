<?php

namespace tiFy\Kernel\Assets;

interface AssetsInterface
{
    /**
     * Définition de styles CSS.
     *
     * @param string $css propriétés CSS.
     * @param string $ui Interface de l'attribut. user|admin|both
     *
     * @return void
     */
    public function addInlineCss($css, $ui = 'user');

    /**
     * Définition d'attributs JS.
     *
     * @param string $key Clé d'indexe de l'attribut à ajouter.
     * @param mixed $value Valeur de l'attribut.
     * @param array $context Contexte d'instanciation de l'attribut. user|admin|both
     * @param bool $in_footer Ecriture des attributs dans le pied de page du site.
     *
     * @return void
     */
    public function setDataJs($key, $value, $context = ['admin', 'user'], $in_footer = true);

    /**
     * Récupération de l'url vers un asset.
     *
     * @param string $path Chemin relatif vers le fichier du dossier des assets.
     *
     * @return string
     */
    public function url($path);
}
