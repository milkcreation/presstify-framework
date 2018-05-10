<?php

namespace tiFy\Form;

use tiFy\Apps\AppController;
use tiFy\Form\Addons\AddonsController;
use tiFy\Form\Buttons\ButtonsController;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Forms\FormBaseController;

final class Form extends AppController
{
    /**
     * Liste des formulaire déclaré
     * @var FormBaseController[]
     */
    protected $registered = [];

    /**
     * Formulaire courant (en cours de traitement)
     * @var
     */
    protected $current;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Déclaration des dépendances
        $this->appServiceShare(AddonsController::class, new AddonsController());
        $this->appServiceShare(ButtonsController::class, new ButtonsController());
        $this->appServiceShare(FieldTypesController::class, new FieldTypesController());
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('init', null, 1);
        $this->appAddAction('wp', null, 0);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        if (is_admin()) :
            $this->registration();
        endif;
    }

    /**
     * A l'issue du chargement complet de Wordpress.
     *
     * @return void
     */
    public function wp()
    {
        if (!is_admin()) :
            $this->registration();
        endif;

    }

    /**
     * Déclaration des formulaires.
     *
     * @return void
     */
    private function registration()
    {
        foreach ($this->appConfig() as $name => $attrs) :
            $this->register($id, $attrs);
        endforeach;

        do_action('tify_form_register', $this);

        do_action('tify_form_loaded');
    }

    /**
     * Déclaration d'un formulaire.
     *
     * @return void
     */
    public function register($name, $attrs = [])
    {
        $alias = "tify.form.{$name}";
        if ($this->appServiceHas($alias)) :
            return;
        endif;

        $this->appServiceShare($alias, new FormBaseController($name, $attrs));

        return $this->registered[$name] = $this->appServiceGet($alias);
    }

    /**
     * Vérification d'existance d'un formulaire déclaré
     *
     * @param string $name Nom de qualification du formulaire.
     *
     * @return null|FormBaseController
     */
    public function has($name)
    {
        return $this->appServiceHas("tify.form.{$name}");
    }

    /**
     * Récupération d'un controleur d'un formulaire déclaré.
     *
     * @param string $name Nom de qualification du formulaire.
     *
     * @return null|FormBaseController
     */
    public function get($name)
    {
        $alias = "tify.form.{$name}";
        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;
    }

    /**
     * Récupération de la liste des formulaires déclarés.
     *
     * @return FormBaseController[]
     */
    public function all()
    {
        return $this->registered;
    }

    /**
     * Définition du formulaire courant.
     *
     * @param string|FormBaseController $form Nom de qualification ou classe de rappel d'un formulaire.
     *
     * @return null|FormBaseController
     */
    /** ==  == **/
    public function setCurrent($form = null)
    {
        if (!is_object($form)) :
            $form = $this->get($form);
        endif;

        if (!$form instanceof FormBaseController) :
            return;
        endif;

        $this->current = $form;
        $this->current->getForm()->onSetCurrent();

        return $this->current;
    }

    /**
     * Récupération du formulaire courant.
     *
     * @return null|FormBaseController
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Réinitialisation du formulaire courant.
     *
     * @return  void
     */
    public function resetCurrent()
    {
        if ($this->current instanceof FormBaseController) :
            $this->current->getForm()->onResetCurrent();
        endif;

        $this->current = null;
    }

    /**
     * Affichage d'un formulaire.
     *
     * @param string $name Nom de qualification du formulaire.
     * @param bool $echo Activation/Désactivation de l'affichage.
     *
     * @return string|void
     */
    public function display($name, $echo = false)
    {
        // Bypass
        if (!$form = $this->setCurrent($name)) :
            return;
        endif;

        $output = "";
        $output .= "\n<div id=\"tiFyForm-{$name}\" class=\"tiFyForm\">";
        $output .= $form->display(false);
        $output .= "\n</div>";

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
}