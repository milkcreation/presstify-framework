<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\FormItemInterface;
use tiFy\Form\Addons\AddonsController;
use tiFy\Form\Buttons\ButtonsController;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Forms\FormBaseController;

final class Form
{
    /**
     * Liste des formulaires déclarés.
     * @var FormItemInterface[]
     */
    protected $items = [];

    /**
     * Formulaire courant.
     * @var FormItemInterface
     */
    protected $current;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function () {
                if (is_admin()) :
                    $this->_init();
                else :
                    add_action(
                        'wp',
                        function () {
                            $this->_init();
                        }, 0
                    );
                endif;
            },
            1
        );
    }

    /**
     * Initialisation des formulaires.
     *
     * @return void
     */
    private function _init()
    {
        if ($forms = config('form', [])) :
            foreach ($forms as $name => $attrs) :
                $this->register($name, $attrs);
            endforeach;

            do_action('tify_form_register', $this);

            do_action('tify_form_loaded');
        endif;
    }

    /**
     * Déclaration d'un formulaire.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Attributs de configuration.
     *
     * @return $this
     */
    public function add($name, $attrs = [])
    {
        config()->set("form.{$name}", $attrs);

        return $this;
    }

    /**
     * Récupération de la liste des instance de formulaires déclarés.
     *
     * @return FormItemInterface[]
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Récupération d'une instance formulaire déclaré.
     *
     * @param string $name Nom de qualification du formulaire.
     *
     * @return null|FormItemInterface
     */
    public function get($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * Récupération du formulaire courant.
     *
     * @return null|FormItemInterface
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Déclaration d'un formulaire.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Attributs de configuration.
     *
     * @return void
     */
    public function register($name, $attrs = [])
    {
        $controller = (isset($attrs['controller'])) ? $attrs['controller'] : FormBaseController::class;

        $resolved = new $controller($name, $attrs);

        return $this->items[$name] = app()
            ->bind(
                "form.{$name}",
                function () use ($resolved) {
                    return $resolved;
                }
            )
            ->build();
    }

    /**
     * Réinitialisation du formulaire courant.
     *
     * @return void
     */
    public function resetCurrent()
    {
        if ($this->current instanceof FormItemInterface) :
            $this->current->getForm()->onResetCurrent();
        endif;

        $this->current = null;
    }

    /**
     * Définition du formulaire courant.
     *
     * @param string|FormItemInterface $form Nom de qualification ou instance du formulaire.
     *
     * @return null|FormItemInterface
     */
    public function setCurrent($form = null)
    {
        if (is_string($form)) :
            $form = $this->get($form);
        endif;

        if (!$form instanceof FormItemInterface) :
            return;
        endif;

        $this->current = $form;
        $this->current->getForm()->onSetCurrent();

        return $this->current;
    }

    /**
     * Affichage d'un formulaire.
     * @deprecated
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