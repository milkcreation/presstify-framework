<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\FormManager;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Factory\ResolverTrait as FormFactoryResolver;
use tiFy\Form\Factory\View;
use tiFy\Kernel\Params\ParamsBag;

class FormFactory extends ParamsBag implements FormFactoryContract
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
     *      @var string $before Pré-affichage, avant la balise <form/>.
     *      @var string $after Post-affichage, après la balise <form/>.
     *      @var string $method Propriété 'method' de la balise <form/>.
     *      @var string $action Propriété 'action' de la balise <form/>.
     *      @var string $enctype Propriété 'enctype' de la balise <form/>.
     *      @var array $attrs Liste des attributs complémentaires de la balise <form/>.
     *      @var boolean|array $grid Activation de l'agencement des éléments.
     *      @var array $addons Liste des attributs des addons actifs.
     *      @var array $buttons Liste des attributs des boutons actifs.
     *      @var array $events Liste des événements de court-circuitage.
     *      @var array $fields Liste des attributs de champs.
     *      @var array $notices Liste des attributs des messages de notification.
     *      @var array $options Liste des options du formulaire.
     *      @var array $viewer Attributs de configuration du gestionnaire de gabarits d'affichage.
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
        'grid'            => false,
        'addons'          => [],
        'buttons'         => [],
        'events'          => [],
        'fields'          => [],
        'notices'         => [],
        'options'         => [],
        'viewer'          => []
    ];

    /**
     * Indicateur de préparation active.
     * @var boolean
     */
    protected $prepared = false;

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
            "form.factory.events.{$this->name()}",
            function () {
                return app()->resolve('form.factory.events', [$this->get('events', []), $this]);
            }
        )->build();

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
            "form.factory.request.{$this->name()}",
            function () {
                return app()->resolve('form.factory.request', [$this]);
            }
        )->build();

        app()->singleton(
            "form.factory.session.{$this->name()}",
            function () {
                return app()->resolve('form.factory.session', [$this]);
            }
        )->build();

        app()->singleton(
            "form.factory.viewer.{$this->name()}",
            function () {
                /** @var FormManager $formManager */
                $formManager = app('form');

                $directory = $formManager->resourcesDir('/views');
                $override_dir = (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                    ? $override_dir
                    : $directory;

                $view = view()
                    ->setDirectory($directory)
                    ->setController(View::class)
                    ->setOverrideDir($override_dir)
                    ->set('form', $this);

                return $view;
            }
        )->build();

        $this->events('form.init', [&$this]);

        $this->boot();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->render();
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
    public function csrf()
    {
        return wp_create_nonce('Form' . $this->name());
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->get('action', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->get('method', 'post');
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->get('title') ? : $this->name();
    }

    /**
     * {@inheritdoc}
     */
    public function hasGrid()
    {
        return !empty($this->get('grid'));
    }

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        return app('form')->index($this->name());
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
    public function onSetCurrent()
    {
        return $this->events('form.set.current', [&$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function onSuccess()
    {
        return $this->request()->get('success') === $this->name();
    }

    /**
     * {@inheritdoc}
     */
    public function onResetCurrent()
    {
        return $this->events('form.reset.current', [&$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->events('form.prepare', [&$this]);

        foreach($this->fields() as $field) :
            $field->prepare();
        endforeach;

        $this->prepared = true;

        $this->events('form.prepared', [&$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if (!$this->prepared) :
            $this->prepare();
        endif;

        $this->renderPrepare();

        $fields = $this->fields();
        $buttons = $this->buttons();
        $notices = $this->notices()->getMessages();

        return $this->form()->viewer('form', compact('buttons', 'fields', 'notices'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderPrepare()
    {
        // Attributs HTML du champ.
        if (!$this->has('attrs.id')) :
            $this->set('attrs.id', "Form-content--{$this->name()}");
        endif;
        if (!$this->get('attrs.id')) :
            $this->pull('attrs.id');
        endif;

        $default_class = "Form-content Form-content--{$this->name()}";
        if (!$this->has('attrs.class')) :
            $this->set('attrs.class', $default_class);
        else :
            $this->set('attrs.class', sprintf($this->get('attrs.class', ''), $default_class));
        endif;
        if (!$this->get('attrs.class')) :
            $this->pull('attrs.class');
        endif;

        $this->set('attrs.action', $this->getAction());
        $this->set('attrs.method', $this->getMethod());
        if ($enctype = $this->get('enctype')) :
            $this->set('attrs.enctype', $enctype);
        endif;

        // Activation de l'agencement des éléments.
        if ($grid = $this->get('grid')) :
            $grid = is_array($grid) ? $grid : [];

            $this->set("attrs.data-grid", 'true');
            $this->set("attrs.data-grid_gutter", $grid['gutter']??0);
        endif;

        if ($this->onSuccess()) :
            $this->notices()->add(
                'success',
                $this->notices()->params('success.message')
            );
            assets()->addInlineJs(
                'if (window.history && window.history.replaceState){'.
                'let location=window.location.href.split("#")[0].split("?")[0];'.
                'window.history.pushState("", document.title, location);};',
                'both',
                true
            );
        endif;

        foreach($this->fields() as $field) :
            $field->renderPrepare();
        endforeach;
    }
}