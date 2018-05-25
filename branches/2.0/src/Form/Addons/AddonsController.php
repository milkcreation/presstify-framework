<?php

namespace tiFy\Form\Addons;

use tiFy\Apps\AppController;
use tiFy\Form\Addons\AddonControllerInterface;
use tiFy\Components\Form\Addons\AjaxSubmit\AjaxSubmit;
use tiFy\Components\Form\Addons\CookieTransport\CookieTransport;
use tiFy\Components\Form\Addons\Mailer\Mailer;
use tiFy\Components\Form\Addons\Preview\Preview;
use tiFy\Components\Form\Addons\Record\Record;
use tiFy\Components\Form\Addons\User\User;
use tiFy\Form\Forms\FormItemController;

final class AddonsController extends AppController
{
    /**
     * Liste des addons prédéfinis
     * @var array
     */
    protected $predefined = [
        //'ajax_submit'      => AjaxSubmit::class,
        //'cookie_transport' => CookieTransport::class,
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
    public function appBoot()
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