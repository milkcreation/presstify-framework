<?php

namespace tiFy\Form\Button;

use tiFy\App\AppController;
use tiFy\Components\Form\Buttons\ButtonControllerInterface;
use tiFy\Components\Form\Buttons\Submit\Submit;
use tiFy\Form\Forms\FormItemController;

final class Manager
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {

    }

    /**
     * Déclaration d'un bouton.
     *
     * @param string $name Nom de qualification du bouton.
     * @param string $classname Nom de la classe de rappel du bouton.
     * @param array $attrs Liste des variables passés en argument dans le controleur du bouton.
     * 
     * @return array
     */
    public function register($name, $classname, $args = [])
    {
        if (in_array($name, array_keys($this->registered))) :
            \wp_die(
                sprintf(__('Le bouton "%s" n\'est pas valide', 'tify'), $name),
                __('Bouton invalide', 'tify'),
                500
            );
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
     * Définition d'un bouton pour un formulaire.
     *
     * @param string $name Nom de qualification du bouton.
     * @param Form $form Classe de rappel du formulaire.
     * @param array $attrs Liste des attributs de configuration du bouton.
     * 
     * @return ButtonControllerInterface
     */
    public function set($name, $form, $attrs = [])
    {
        if (! isset($this->registered[$name])) :
            return;
        endif;

        $classname = $this->registered[$name]['controller'];

        $instance = new $classname($this->registered[$name]['args']);
        $instance->make($name, $form, $attrs);

        return $instance;
    }
}