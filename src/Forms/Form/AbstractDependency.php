<?php

namespace tiFy\Forms\Form;

use tiFy\Apps\AppTrait;
use tiFy\Forms\Form\Field;
use tiFy\Forms\Form\Form;

abstract class AbstractDependency extends \tiFy\App
{
    use Apptrait;

    /**
     * Classe de rappel du formulaire associé.
     * @var null|Form
     */
    protected $form;

    /**
     * CONSTRUCTEUR
     *
     * @param Form $Form Formulaire de référence
     *
     * @return void
     */
    public function __construct(Form $Form)
    {
        // Définition du formulaire de référence
        $this->form = $Form;
    }

    /**
     * Récupération du formulaire de référence
     *
     * @return Form
     */
    final public function getForm()
    {
        return $this->form;
    }

    /**
     * Récupération d'un champ selon son identifiant de qualification
     *
     * @param string $slug Identifiant de qualification
     *
     * @return null|Field
     */
    final public function getField($slug)
    {
        if ($form = $this->getForm()) :
            return $form->getField($slug);
        endif;
    }

    /**
     * Récupération d'un addon du formulaire de référence
     *
     * @param string $name Nom de qualification de l'addon
     *
     * @return null|\tiFy\Forms\Addons\Factory
     */
    final public function getAddon($name)
    {
        if ($form = $this->getForm()) :
            return $form->getAddon($name);
        endif;
    }

    /**
     * Récupération de la classe de rappel des notifications
     *
     * @return null|\tiFy\Forms\Form\Notices
     */
    final public function getNotices()
    {
        // Bypass
        if ($form = $this->getForm()) :
            return $this->getForm()->notices();
        endif;
    }

    /**
     * Vérification d'existance d'erreur de traitement
     *
     * @return bool
     */
    public function hasError()
    {
        // Bypass
        if (!$notices = $this->getNotices()) :
            return false;
        endif;

        return $notices->has('error');
    }

    /**
     * Ajout d'une notification d'erreur de traitement
     *
     * @param string $message
     * @param mixed $data
     *
     * @return void
     */
    public function addError($message, $data = '')
    {
        // Bypass
        if (!$notices = $this->getNotices()) :
            return null;
        endif;

        return $notices->add('error', $message, $data);
    }

    /**
     * Récupération de la liste des notifications d'erreur de traitement
     *
     * @param array $args Attributs de récupération de la liste des erreurs
     *
     * @return array
     */
    public function getErrors($args = [])
    {
        // Bypass
        if (!$notices = $this->getNotices()) :
            return [];
        endif;

        return $notices->getByData('error', $args);
    }
}