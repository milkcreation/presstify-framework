<?php

namespace tiFy\Form;

use tiFy\Form\Addons\AddonControllerInterface;
use tiFy\Form\Fields\FieldItemCollectionController;
use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Forms\FormBaseController;
use tiFy\Form\Forms\FormItemController;
use tiFy\Form\Forms\FormHandleController;
use tiFy\Form\Forms\FormNoticesController;
use tiFy\Form\Forms\FormTransportController;
use tiFy\Kernel\Tools;

trait CommonDependencyAwareTrait
{
    /**
     * Classe de rappel du controleur de formulaire associé.
     * @var null|FormItemController
     */
    protected $form;

    /**
     * ADDONS
     */
    /**
     * Récupération de la liste des addons.
     *
     * @return null|AddonControllerInterface[]
     */
    public function getAddons()
    {
        return $this->getForm()->addons();
    }

    /**
     * Récupération d'un addon du formulaire de référence.
     *
     * @param string $name Nom de qualification de l'addon.
     *
     * @return null|AddonControllerInterface
     */
    public function getAddon($name)
    {
        return $this->getForm()->getAddon($name);
    }

    /**
     * FORMS
     */
    /**
     * Récupération du formulaire de référence.
     *
     * @return FormItemController
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Définition du formulaire de référence.
     *
     * @param FormItemController $form  Classe de rappel du controleur de formulaire associé.
     *
     * @return void
     */
    public function setForm($form)
    {
        return $this->form = $form;
    }

    /**
     * FIELDS
     */
    /**
     * Récupération de la liste des champs.
     *
     * @return null|FieldItemCollectionController
     */
    public function getFields()
    {
        return $this->getForm()->fields();
    }

    /**
     * Récupération d'un champ selon son identifiant de qualification.
     *
     * @param string $slug Identifiant de qualification du champ.
     *
     * @return null|FieldItemController
     */
    public function getField($slug)
    {
        return $this->getFields()->getField($slug);
    }

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
}