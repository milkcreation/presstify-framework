<?php

namespace tiFy\Form\Addon;

use tiFy\Contracts\Form\AddonFactory;

final class Manager
{
    /**
     * Liste des formulaires actifs par addon.
     * @var array 
     */
    protected $activeForms = [];

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Déclaration d'un addon.
     *
     * @param string $name Nom de qualification de l'addon.
     * @param string $classname Nom de la classe de rappel de l'addon.
     * @param array $attrs Liste des variables passés en argument dans le controleur de l'addon.
     *
     * @return array
     */
    public function register($name, $classname, $args = [])
    {
        if (in_array($name, array_keys($this->registered))) :
            return;
        endif;

        if (! class_exists($classname)) :
            return;
        endif;

        return $this->registered[$name] = [
            'controller' => $classname,
            'args'       => $args
        ];
    }

    /**
     * Définition d'un addon pour un formulaire.
     *
     * @param string $name Nom de qualification de l'addon.
     * @param Form $form Classe de rappel du formulaire.
     *
     * @return AddonControllerInterface
     */
    public function set($name, $form)
    {
        if (! isset($this->registered[$name])) :
            \wp_die(
                sprintf(__('L\'addon "%s" n\'est pas valide', 'tify'), $name),
                __('Addon invalide', 'tify'),
                500
            );
        endif;

        $classname = $this->registered[$name]['controller'];

        $instance = new $classname($this->registered[$name]['args']);
        $instance->make($name, $form);

        // Définition de la liste des formulaires actif pour cet addon
        if (! isset($this->activeForms[$name])) :
            $this->activeForms[$name] = [];
        endif;
        array_push($this->activeForms[$name], $form);

        return $instance;
    }

    /**
     * Récupération de la liste des formulaires actif pour un formulaire
     *
     * @return array|Form[]
     */
    public function activeForms($name)
    {
        if (isset($this->activeForms[$name])) :
            return $this->activeForms[$name];
        endif;

        return [];
    }
}