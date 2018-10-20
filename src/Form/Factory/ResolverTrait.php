<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\AddonFactory;
use tiFy\Contracts\Form\FactoryAddons;
use tiFy\Contracts\Form\FactoryButtons;
use tiFy\Contracts\Form\FactoryDisplay;
use tiFy\Contracts\Form\FactoryEvents;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FactoryFields;
use tiFy\Contracts\Form\FactoryNotices;
use tiFy\Contracts\Form\FactoryOptions;
use tiFy\Contracts\Form\FactorySession;
use tiFy\Contracts\Form\FactoryValidation;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Kernel\Tools;

trait ResolverTrait
{
    /**
     * Instance du controleur de champ associé.
     * @var FactoryField
     */
    protected $field;

    /**
     * Instance du controleur de formulaire associé.
     * @var FormFactory
     */
    protected $form;

    /**
     * Récupération de l'instance du contrôleur d'un addon associé au formulaire.
     *
     * @param string $name Nom de qualification de l'addon.
     *
     * @return AddonFactory
     */
    public function addon($name)
    {
        return $this->addons()->get($name);
    }

    /**
     * Récupération de l'instance du contrôleur des addons associés au formulaire.
     *
     * @return FactoryAddons
     */
    public function addons()
    {
        return app()->resolve("form.factory.addons.{$this->form()->name()}");
    }

    /**
     * Récupération de l'instance du contrôleur des boutons associés au formulaire.
     *
     * @return FactoryButtons
     */
    public function buttons()
    {
        return app()->resolve("form.factory.buttons.{$this->form()->name()}");
    }

    /**
     * Récupération de l'instance du contrôleur des événements associés au formulaire ou déclenchement d'un événement.
     *
     * @param string $name Nom de qualification de l'événement.
     *
     * @return mixed|FactoryEvents
     */
    public function events($name = null)
    {
        /** @var FactoryEvents $factory */
        $factory = app()->resolve("form.factory.events.{$this->form()->name()}");

        if (is_null($name)) :
            return $factory;
        endif;

        return call_user_func_array([$factory, 'trigger'], func_get_args());
    }

    /**
     * Récupération de l'instance du contrôleur d'un champ associé au formulaire.
     *
     * @param null|string $name Nom de qualification de l'addon.
     *
     * @return FactoryField
     */
    public function field($slug = null)
    {
        if (is_null($slug)) :
            return $this->field;
        endif;

        return $this->fields()->get($slug);
    }

    /**
     * Récupération de l'instance du contrôleur des champs associés au formulaire.
     *
     * @return FactoryFields
     */
    public function fields()
    {
        return app()->resolve("form.factory.fields.{$this->form()->name()}");
    }

    /**
     * Récupération de l'instance du formulaire.
     *
     * @return FormFactoryInterface
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * Récupération de l'instance du contrôleur des messages de notification associés au formulaire.
     *
     * @return FactoryNotices
     */
    public function notices()
    {
        return app()->resolve("form.factory.notices.{$this->form()->name()}");
    }

    /**
     * Récupération d'une option ou de la liste complète des options du formulaire.
     *
     * @return mixed
     */
    public function option($key = null, $default = null)
    {
        if (is_null($key)) :
            return $this->options()->all();
        endif;

        return $this->options()->get($key, $default);
    }

    /**
     * Récupération de l'instance du contrôleur des options associées au formulaire.
     *
     * @return FactoryOptions
     */
    public function options()
    {
        return app()->resolve("form.factory.options.{$this->form()->name()}");
    }

    /**
     * Récupération de l'instance du contrôleur de session associé au formulaire.
     *
     * @return FactorySession
     */
    public function session()
    {
        return app()->resolve("form.factory.session.{$this->form()->name()}");
    }

    /**
     * Récupération de l'instance du contrôleur de validation associé au formulaire.
     *
     * @return FactoryValidation
     */
    public function validation()
    {
        return app()->resolve("form.factory.validation.{$this->form()->name()}");
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     */

    /**
     * Récupération de la liste des valeurs des champs.
     *
     * @param bool $raw Récupération des valeurs brutes.
     *
     * @return array
     */
    public function getFieldsValues($raw = false)
    {
        $values = [];
        foreach ($this->getFields() as $field) :
            $values[$field->getSlug()] = $field->getValue($raw);
        endforeach;

        return $values;
    }

    /**
     * CALLBACKS
     */
    /**
     * Appel des fonctions de court-circuitage de traitement du formulaire.
     *
     * @param string $hookname Nom de qualification du court-circuitage.
     * @param array $args Liste des variables passées en arguments dans les fonctions.
     *
     * @return mixed
     */
    public function call($hookname, $args = [])
    {
        return $this->getForm()->callbacks()->call($hookname, $args);
    }

    /**
     * CONTROLLER
     */
    /**
     * Récupération de la classe de rappel du controleur de surchage du formulaire.
     *
     * @return FormBaseController
     */
    public function getController()
    {
        return $this->getForm()->controller();
    }

    /**
     * HANDLE
     */
    /**
     * Récupération de la classe de rappel du controleur de traitement de la soumission du formulaire.
     *
     * @return FormHandleController
     */
    public function getHandle()
    {
        return $this->getForm()->handle();
    }

    /**
     * NOTICES
     */
    /**
     * Récupération de la classe de rappel des notifications.
     *
     * @return null|FormNoticesController
     */
    public function getNotices()
    {
        return $this->getForm()->notices();
    }

    /**
     * Vérification d'existance d'erreur de traitement.
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->getNotices()->has('error');
    }

    /**
     * Ajout d'une notification d'erreur de traitement.
     *
     * @param string $message
     * @param mixed $data
     *
     * @return void
     */
    public function addError($message, $data = '')
    {
        return $this->getNotices()->add('error', $message, $data);
    }

    /**
     * Requête de récupération de la liste des notifications d'erreur de traitement.
     *
     * @param array $args Attributs de récupération de la liste des erreurs.
     *
     * @return array
     */
    public function queryErrors($args = [])
    {
        return $this->getNotices()->query('error', $args);
    }

    /**
     * TRANSPORT
     */
    /**
     * Récupération du controleur de gestion des données embarquées.
     *
     * @return FormTransportController
     */
    public function getTransport()
    {
        return $this->getForm()->transport();
    }

    /**
     * Récupération l'identifiant de session.
     *
     * @return string
     */
    public function getSession()
    {
        return $this->getTransport()->getSession();
    }

    /**
     * FUNCTIONS
     */
    /**
     * Traitement recursif d'une liste des arguments.
     *
     * @param array $args Liste des arguments à traiter.
     * @param array $defaults Liste des arguments par defaut.
     *
     * @return array
     */
    public function recursiveParseArgs($args, $defaults)
    {
        $_args = [];
        if (!$args) :
            $_args = $defaults;
        elseif (!$defaults) :
            $_args = $args;
        elseif (is_array($args)) :
            foreach ((array)$defaults as $key => $default) :
                if (!is_array($default)) :
                    if (isset($args[$key])) :
                        $_args[$key] = $args[$key];
                    else :
                        $_args[$key] = $default;
                    endif;
                else :
                    if (isset($args[$key]) && is_array($args[$key])) :
                        $_args[$key] = $this->recursiveParseArgs($args[$key], $default);
                    elseif (isset($args[$key]))    :
                        $_args[$key] = $args[$key];
                    else :
                        $_args[$key] = $default;
                    endif;
                endif;

                if (isset($args[$key]) && is_array($args[$key])) :
                    unset($args[$key]);
                endif;
                unset($defaults[$key]);
            endforeach;
            $_args += $args;
        endif;

        return $_args;
    }

    /**
     * Traitement de la liste des attributs HTML.
     *
     * @param array $attrs Liste des attributs HTML.
     * @param bool $linearized Activation de la linéarisation.
     *
     * @return string
     */
    public function parseHtmlAttrs($attrs = [], $linearized = true)
    {
        return Tools::Html()->parseAttrs($attrs, $linearized);
    }

    /**
     * Récupération des variables de champ.
     *
     * @param mixed $vars
     *
     * @return string
     */
    public function parseFieldVars($vars)
    {
        if (is_string($vars)) :
            if (preg_match_all('#([^%%]*)%%(.*?)%%([^%%]*)?#', $vars, $matches)) :
                $vars = "";
                foreach ($matches[2] as $i => $slug) :
                    $vars .= $matches[1][$i] . (($field = $this->getField($slug)) ? $field->getValue() : $matches[2][$i]) . $matches[3][$i];
                endforeach;
            endif;
        elseif (is_array($vars)) :
            foreach ($vars as $k => &$i) :
                $i = $this->parseFieldVars($i);
            endforeach;
        endif;

        return $vars;
    }
}