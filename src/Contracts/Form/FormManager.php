<?php

namespace tiFy\Contracts\Form;

interface FormManager
{
    /**
     * Déclaration d'un formulaire.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Attributs de configuration.
     *
     * @return $this
     */
    public function add($name, $attrs = []);

    /**
     * Récupération de la liste des instance de formulaires déclarés.
     *
     * @return FormFactory[]
     */
    public function all();

    /**
     * Récupération ou définition du formulaire courant.
     *
     * @param string|FormFactory $form Nom de qualification ou instance du formulaire.
     *
     * @return null|FormFactory
     */
    public function current($form = null);

    /**
     * Récupération d'une instance formulaire déclaré.
     *
     * @param string $name Nom de qualification du formulaire.
     *
     * @return null|FormFactory
     */
    public function get($name);

    /**
     * Récupération du numéro d'indice d'un formulaire déclaré.
     *
     * @param string $name Nom de qualification du formulaire.
     *
     * @return null|int
     */
    public function index($name);

    /**
     * Réinitialisation du formulaire courant.
     *
     * @return void
     */
    public function reset();

    /**
     * Récupération du chemin absolu vers le répertoire des ressources.
     *
     * @param string $path Chemin relatif du sous-repertoire.
     *
     * @return string
     */
    public function resourcesDir($path = '');

    /**
     * Récupération de l'url absolue vers le répertoire des ressources.
     *
     * @param string $path Chemin relatif du sous-repertoire.
     *
     * @return string
     */
    public function resourcesUrl($path = '');
}