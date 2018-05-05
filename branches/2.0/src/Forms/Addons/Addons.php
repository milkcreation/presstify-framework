<?php

namespace tiFy\Forms\Addons;

use tiFy\Apps\AppController;
use tiFy\Forms\Addons\AddonControllerInterface;
use tiFy\Forms\Addons\AjaxSubmit\AjaxSubmit;
use tiFy\Forms\Addons\CookieTransport\CookieTransport;
use tiFy\Forms\Addons\Mailer\Mailer;
use tiFy\Forms\Addons\Preview\Preview;
use tiFy\Forms\Addons\Record\Record;
use tiFy\Forms\Addons\User\User;
use tiFy\Forms\Form\Form;

final class Addons extends AppController
{
    /**
     * Liste des addons prédéfinis
     * @var array
     */
    protected $predefined = [
        'ajax_submit'      => AjaxSubmit::class,
        'cookie_transport' => CookieTransport::class,
        'mailer'           => Mailer::class,
        //'preview'         => Preview::class,
        'record'           => Record::class,
        'user'             => User::class,
    ];

    /**
     * Liste des addons déclarés.
     * @var array
     */
    protected $registered = [];

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
    public function boot()
    {
        foreach ($this->predefined as $name => $classname) :
            $this->register($name, $classname);
        endforeach;

        do_action('tify_form_addon_register', $this);
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
     * @param array $attrs Liste des attributs de configuration de l'addon.
     *
     * @return AddonControllerInterface
     */
    public function set($name, $form, $attrs = [])
    {
        if (! isset($this->registered[$name])) :
            return;
        endif;

        $classname = $this->registered[$name]['controller'];

        $instance = new $classname($this->registered[$name]['args']);
        $instance->make($name, $form, $attrs);

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