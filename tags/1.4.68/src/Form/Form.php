<?php

namespace tiFy\Form;

use tiFy\App\Dependency\AbstractAppDependency;
use tiFy\Form\Addons\AddonsController;
use tiFy\Form\Buttons\ButtonsController;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Forms\FormBaseController;

final class Form extends AbstractAppDependency
{
    /**
     * Liste des formulaires déclarés.
     * @var FormBaseController[]
     */
    protected $registered = [];

    /**
     * Formulaire courant.
     * @var FormBaseController
     */
    protected $current;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        \add_shortcode('formulaire', [$this, 'shortcode']);

        $this->app->appAddAction('init', [$this, 'init'], 1);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        if (config('form', [])) :
            $this->app->singleton(AddonsController::class);
            $this->app->singleton(ButtonsController::class);
            $this->app->singleton(FieldTypesController::class);

            if (is_admin()) :
                $this->registration();
            else :
                $this->app->appAddAction(
                    'wp',
                    function () {
                        $this->registration();
                    }, 0
                );
            endif;
        endif;
    }

    /**
     * Shortcode d'affichage de formulaire.
     *
     * @param array $atts Attributs de configuration.
     *
     * @return string
     */
    public function shortcode($atts = [])
    {
        extract(
            shortcode_atts(
                ['name' => null],
                $atts
            )
        );

        return $this->display($name);
    }

    /**
     * Déclaration des formulaires.
     *
     * @return void
     */
    private function registration()
    {
        foreach (config('form', []) as $name => $attrs) :
            $this->register($name, $attrs);
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
        if ($this->app->has($alias)) :
            return;
        endif;

        $controller = (isset($attrs['controller'])) ? $attrs['controller'] : FormBaseController::class;

        $this->app->bind($alias, new $controller($name, $attrs));

        return $this->registered[$name] = $this->app->resolve($alias);
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
        return $this->app->has("tify.form.{$name}");
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
        if ($this->app->has($alias)) :
            return $this->app->resolve($alias);
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