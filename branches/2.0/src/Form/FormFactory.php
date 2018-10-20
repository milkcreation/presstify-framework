<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\Manager;
use tiFy\Contracts\Form\FormFactory as FormFactoryInterface;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Form\Factory\ResolverTrait as FormFactoryResolver;
use tiFy\Form\FormView;
use tiFy\Kernel\Parameters\ParamsBagController;

class FormFactory extends ParamsBagController implements FormFactoryInterface
{
    use FormFactoryResolver;

    /**
     * Nom de qualification du formulaire.
     * @var string
     */
    protected $name = '';

    /**
     * Listes des attributs de configuration.
     * @var array {
     *      @var string $title Intitulé de qualification du formulaire.
     *      @var bool|array $wrapper Activation de l'encapsulation HTML, paramètres par défaut|Liste des attributs de balise HTML.
     *      @var string $before Pré-affichage, avant la balise <form/>.
     *      @var string $after Post-affichage, après la balise <form/>.
     *      @var string $method Propriété 'method' de la balise <form/>.
     *      @var string $action Propriété 'action' de la balise <form/>.
     *      @var string $enctype Propriété 'enctype' de la balise <form/>.
     *      @var array $attrs Liste des attributs complémentaires de la balise <form/>.
     *      @var array $addons Liste des attributs des addons actifs.
     *      @var array $buttons Liste des attributs des boutons actifs.
     *      @var array $fields Liste des attributs de champs.
     *      @var array $notices Liste des attributs des messages de notification.
     *      @var array $options Liste des options du formulaire.
     *      @var array $callbacks Liste des fonctions et méthodes de court-circuitage de traitement du formulaire.
     * }
     */
    protected $attributes = [
        'title'           => '',
        'before'          => '',
        'after'           => '',
        'method'          => 'post',
        'action'          => '',
        'enctype'         => '',
        'attrs'           => [],
        'addons'          => [],
        'buttons'         => [],
        'events'          => [],
        'fields'          => [],
        'notices'         => [],
        'options'         => [],
        'viewer'          => []
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du formulaire.
     * @param array $attrs Liste des attributs de configuration
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {

        $this->name = $name;
        $this->form = $this;

        parent::__construct($attrs);

        app()->singleton(
            "form.factory.addons.{$this->name()}",
            function () {
                return app()->resolve('form.factory.addons', [$this->get('addons', []), $this]);
            }
        )->build();

        app()->singleton(
            "form.factory.buttons.{$this->name()}",
            function () {
                return app()->resolve('form.factory.buttons', [$this->get('buttons', []), $this]);
            }
        )->build();

        app()->singleton(
            "form.factory.display.{$this->name()}",
            function () {
                return app()->resolve('form.factory.display', [$this]);
            }
        )->build();

        app()->singleton(
            "form.factory.events.{$this->name()}",
            function () {
                return app()->resolve('form.factory.events', [$this->get('events', []), $this]);
            }
        )->build();

        app()->singleton(
            "form.factory.fields.{$this->name()}",
            function () {
                return app()->resolve('form.factory.fields', [$this->get('fields', []), $this]);
            }
        )->build();

        app()->singleton(
            "form.factory.notices.{$this->name()}",
            function () {
                return app()->resolve('form.factory.notices', [$this->get('notices', []), $this]);
            }
        )->build();

        app()->singleton(
            "form.factory.options.{$this->name()}",
            function () {
                return app()->resolve('form.factory.options', [$this->get('options', []), $this]);
            }
        )->build();

        app()->singleton(
            "form.factory.session.{$this->name()}",
            function () {
                return app()->resolve('form.factory.session', [$this]);
            }
        )->build();

        app()->singleton(
            "form.factory.validation.{$this->name()}",
            function () {
                return app()->resolve('form.factory.validation', [$this]);
            }
        )->build();

        $viewer = app()->singleton(
            "form.factory.viewer.{$this->name()}",
            function () {
                /** @var Manager $manager */
                $manager = app('form');

                $directory = $manager->resourcesDir('/views');
                $override_dir = (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                    ? $override_dir
                    : $directory;

                $view = view()
                    ->setDirectory($directory)
                    ->setController(FormView::class)
                    ->setOverrideDir($override_dir)
                    ->set('form', $this);

                return $view;
            }
        )->build();

        $this->events()->listen(
            'field.init.value',
            function($value, $field) {
                var_dump('tutu');
                exit;
            }
        );


        $this->events('form.init', [&$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->display();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        return app()->resolve("form.factory.display.{$this->name()}");
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        /** @var ViewsInterface $viewer */
        $viewer = app()->resolve("form.factory.viewer.{$this->name()}");

        if (is_null($view)) :
            return $viewer;
        endif;

        return $viewer->make("_override::{$view}", $data);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     */
    /**
     * Traitement de la liste des options d'un addon.
     *
     * @param string $name Nom de qualification de l'addon.
     * @param array $default Liste des options par défaut.
     *
     * @return void
     */
    public function parseDefaultAddonOptions($name, $default = [])
    {
        $this->set(
            "addons.{$name}",
            $this->recursiveParseArgs($this->getAddonOptions($name), $default)
        );
    }

    /**
     * Récupération de l'action du formulaire (url).
     *
     * @return string
     */
    public function getAction()
    {
        return $this->get('action', '');
    }

    /**
     * Récupération d'une option d'un addon.
     *
     * @param string $name Nom de qualification de l'addon.
     * @param string $key Clé d'index de l'option à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getAddonOption($name, $key, $default = '')
    {
        return $this->get("addons.{$name}.$key", $default);
    }

    /**
     * Récupération de la liste des options d'un addon.
     *
     * @param string $name Nom de qualification de l'addon.
     *
     * @return array
     */
    public function getAddonOptions($name)
    {
        return $this->get("addons.{$name}", []);
    }

    /**
     * Récupération de la méthode de soumission du formulaire.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->get('method', 'post');
    }

    /**
     * Récupération de la clé d'indexe de sécurisation du formulaire (CSRF).
     *
     * @return string
     */
    public function getNonce()
    {
        return "_{$this->getUid()}_nonce";
    }

    /**
     * Récupération du préfixe de formulaire.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->get('prefix', 'tiFyForm_');
    }

    /**
     * Récupération de l'intitulé de qualification du formulaire.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title') ? : $this->name();
    }

    /**
     * Récupération de l'identifiant unique de qualification du formulaire.
     *
     * @return string
     */
    public function getUid()
    {
        return $this->getPrefix() . $this->name();
    }

    /**
     * Evénement de déclenchement à l'initialisation du formulaire en tant que formulaire courant.
     *
     * @return void
     */
    public function onSetCurrent()
    {
        return $this->events('form.set.current', [&$this]);
    }

    /**
     * Evénement de déclenchement à la réinitialisation du formulaire courant du formulaire.
     *
     * @return void
     */
    public function onResetCurrent()
    {
        return $this->events('form.reset.current', [&$this]);
    }
}