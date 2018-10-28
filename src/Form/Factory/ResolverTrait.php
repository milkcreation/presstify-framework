<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\AddonController;
use tiFy\Contracts\Form\ButtonController;
use tiFy\Contracts\Form\FactoryAddons;
use tiFy\Contracts\Form\FactoryButtons;
use tiFy\Contracts\Form\FactoryEvents;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FactoryFields;
use tiFy\Contracts\Form\FactoryNotices;
use tiFy\Contracts\Form\FactoryOptions;
use tiFy\Contracts\Form\FactoryRequest;
use tiFy\Contracts\Form\FactorySession;
use tiFy\Contracts\Form\FormFactory;

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
     * @return FactoryAddons|AddonController[]
     */
    public function addons()
    {
        return app()->resolve("form.factory.addons.{$this->form()->name()}");
    }

    /**
     * Récupération de l'instance du contrôleur des boutons associés au formulaire.
     *
     * @return FactoryButtons|ButtonController[]
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
     * @return FactoryFields|FactoryField[]
     */
    public function fields()
    {
        return app()->resolve("form.factory.fields.{$this->form()->name()}");
    }

    /**
     * Récupération de valeur(s) de champ(s) basée(s) sur leurs variables d'identifiant de qualification.
     *
     * @param mixed $tags Variables de qualification de champs.
     * string ex. "%%{{slug#1}}%% %%{{slug#2}}%%"
     * array ex ["%%{{slug#1}}%%", "%%{{slug#2}}%%"]
     * @param boolean $raw Activation de la valeur de retour au format brut.
     *
     * @return string
     */
    public function fieldTagValue($tags, $raw = true)
    {
        if (is_string($tags)) :
            if (preg_match_all('#([^%%]*)%%(.*?)%%([^%%]*)?#', $tags, $matches)) :
                $tags = '';
                foreach ($matches[2] as $i => $slug) :
                    $tags .= $matches[1][$i] . (($field = $this->field($slug)) ? $field->getValue($raw) : $matches[2][$i]) . $matches[3][$i];
                endforeach;
            endif;
        elseif (is_array($tags)) :
            foreach ($tags as $k => &$i) :
                $i = $this->parseFieldTag($i);
            endforeach;
        endif;

        return $tags;
    }

    /**
     * Récupération de l'instance du formulaire.
     *
     * @return FormFactory
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
     * Récupération de l'instance du contrôleur de traitement associé au formulaire.
     *
     * @return FactoryRequest
     */
    public function request()
    {
        return app()->resolve("form.factory.request.{$this->form()->name()}");
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
}