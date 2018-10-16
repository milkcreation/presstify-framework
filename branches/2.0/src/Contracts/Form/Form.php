<?php

namespace tiFy\Contracts\Form;

interface Form
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
     * @return FormItem[]
     */
    public function all();

    /**
     * Récupération ou définition du formulaire courant.
     *
     * @param string|FormItem $form Nom de qualification ou instance du formulaire.
     *
     * @return null|FormItem
     */
    public function current($form = null);

    /**
     * Récupération d'une instance formulaire déclaré.
     *
     * @param string $name Nom de qualification du formulaire.
     *
     * @return null|FormItem
     */
    public function get($name);

    /**
     * Réinitialisation du formulaire courant.
     *
     * @return void
     */
    public function reset();
}