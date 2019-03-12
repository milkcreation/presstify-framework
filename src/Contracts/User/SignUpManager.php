<?php

namespace tiFy\Contracts\User;

interface SignUpManager
{
    /**
     * Déclaration d'un formulaire d'inscription.
     *
     * @param string $name Identifiant de qualification du formulaire.
     * @param array $attrs Attributs de configuration.
     *
     * @return void
     */
    public function _register($name, $attrs = []);

    /**
     * Enregistrement d'un formulaire d'inscription.
     *
     * @param string $name Identifiant de qualification du formulaire.
     * @param array $attrs Attributs de configuration.
     *
     * @return $this
     */
    public function add($name, $attrs = []);

    /**
     * Récupération d'un formulaire d'inscription.
     *
     * @param string $name Identifiant de qualification.
     *
     * @return null|SignupController
     */
    public function get($name);
}