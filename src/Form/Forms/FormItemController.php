<?php

namespace tiFy\Form\Forms;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use tiFy\Apps\AppController;
use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Addons\AddonControllerInterface;
use tiFy\Form\Addons\AddonsController;
use tiFy\Form\Buttons\ButtonControllerInterface;
use tiFy\Form\Buttons\ButtonsController;
use tiFy\Form\Fields\FieldItemCollectionController;
use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Fields\FieldTypeControllerInterface;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Forms\FormBaseController;
use tiFy\Form\Forms\FormCallbacksController;
use tiFy\Form\Forms\FormDisplayController;
use tiFy\Form\Forms\FormHandleController;
use tiFy\Form\Forms\FormNoticesController;
use tiFy\Form\Forms\FormOptionsController;
use tiFy\Form\Forms\FormTransportController;

class FormItemController extends AbstractCommonDependency
{
    /**
     * Nom de qualification du formulaire.
     * @var string
     */
    protected $name = '';

    /**
     * Listes des attributs de configuration.
     * @var array {
     *
     * @var string $title Intitulé de qualification du formulaire.
     * @var bool|array $wrapper Activation de l'encapsulation HTML, paramètres par défaut|Liste des attributs de balise HTML.
     * @var string $before Pré-affichage, avant la balise <form/>.
     * @var string $after Post-affichage, après la balise <form/>.
     * @var string $method Propriété 'method' de la balise <form/>.
     * @var string $action Propriété 'action' de la balise <form/>.
     * @var string $enctype Propriété 'enctype' de la balise <form/>.
     * @var array $attrs Liste des attributs complémentaires de la balise <form/>.
     * @var array $addons Liste des attributs des addons actifs.
     * @var array $buttons Liste des attributs des boutons actifs.
     * @var array $fields Liste des attributs de champs.
     * @var array $notices Liste des attributs des messages de notification.
     * @var array $options Liste des options du formulaire.
     * @var array $callbacks Liste des fonctions eet méthode de court-circuitage de traitement du formulaire.
     * }
     */
    protected $attributes = [
        'prefix'          => '',
        'title'           => '',
        'wrapper'         => true,
        'before'          => '',
        'after'           => '',
        'method'          => 'post',
        'action'          => '',
        'enctype'         => '',
        'attrs'           => [],
        'addons'          => [],
        'buttons'         => [],
        'fields'          => [],
        'notices'         => [],
        'options'         => [],
        'callbacks'       => [],
    ];

    /**
     * Liste des controleurs des addons actifs.
     * @var AddonControllerInterface[]
     */
    protected $addons = [];

    /**
     * Liste des controleurs des boutons actifs.
     * @var ButtonControllerInterface[]
     */
    protected $buttons = [];

    /**
     * Controleur des fonctions de rappel de court-circuitage.
     * @var FormCallbacksController
     */
    protected $callbacks;

    /**
     * Controleur de base.
     * @var FormBaseController
     */
    protected $controller;

    /**
     * Liste des controleurs de la liste des champs.
     * @var FieldItemCollectionController
     */
    protected $fields = [];

    /**
     * Controleur de traitement de la requête de soumission du formulaire.
     * @var FormHandleController
     */
    protected $handle;

    /**
     * Controleur de gestion des messages de notification.
     * @var FormNoticesController
     */
    protected $notices;

    /**
     * Controleur de gestion des options.
     * @var FormOptionsController
     */
    protected $options;

    /**
     * Controleur de gestion des données embarquées.
     * @var FormTransportController
     */
    protected $transport;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du formulaire.
     * @param array $attrs Liste des attributs de configuration
     *
     * @return void
     */
    public function __construct($name, $attrs = [], FormBaseController $controller)
    {
        parent::__construct($this);

        $this->name = $name;
        $this->controller = $controller;

        $this->_initAttributes($attrs);

        $this->_initCallbacks();

        $this->_initButtons();

        $this->_initAddons();

        $this->_initNotices();

        $this->_initOptions();

        $this->_initFields();

        $this->_initTransport();

        $this->_initHandle();
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->call('form_init', [&$this]);
    }

    /**
     * Initialisation de la liste des addons.
     *
     * @return void
     */
    private function _initAddons()
    {
        foreach ($this->get('addons', []) as $k => $v) :
            if (!$v) :
                continue;
            elseif (is_numeric($k)) :
                $name = $v;
            else :
                $name = $k;
            endif;

            $this->addons[$name] = $this->appServiceGet(AddonsController::class)->set($name, $this);
        endforeach;
    }

    /**
     * Initialisation de la liste des attributs de configuration.
     *
     * @param array $attrs Liste des attributs.
     *
     * @return void
     */
    private function _initAttributes($attrs = [])
    {
        $this->attributes = $this->recursiveParseArgs($attrs, $this->attributes);
    }

    /**
     * Initialisation de la liste des boutons.
     *
     * @return void
     */
    private function _initButtons()
    {
        $disabled = [];
        foreach ($this->get('buttons', []) as $k => $v) :
            if (!$v) :
                array_push($disabled, $k);
                continue;
            elseif (is_numeric($k)) :
                $name = $v;
                $attrs = [];
            else :
                $name = $k;
                $attrs = (array)$v;
            endif;

            $this->buttons[$name] = $this->appServiceGet(ButtonsController::class)->set($name, $this, $attrs);
        endforeach;

        if (!isset($this->buttons['submit']) && !in_array('submit', $disabled)) :
            $this->buttons['submit'] = $this->appServiceGet(ButtonsController::class)->set('submit', $this);
        endif;
    }

    /**
     * Initialisation des méthodes de rappel de court-circuitage de traitement du formulaire.
     *
     * @return void
     */
    private function _initCallbacks()
    {
        $this->callbacks = new FormCallbacksController($this, $this->get('callbacks', []));
    }

    /**
     * Initialisation de la liste des champs.
     *
     * @return void
     */
    private function _initFields()
    {
        $fields = [];
        foreach ($this->get('fields', []) as $attrs) :
            $instance = new FieldItemController($this, $attrs);
            $fields[] = $instance;
        endforeach;

        // Ordonnancement
        $this->fields = new FieldItemCollectionController($fields);
        foreach($this->fields->byGroup() as $group) :
            $max = $group->max(function($item){return $item->get('order', 0);}) ? : count($group);
            $pad = 0;
            $group->each(function($item, $key) use(&$pad, $max) {
                $number = 1000*($item->getGroup()+1);
                $order = $item->get('order', 0) ? : ++$pad+$max;
                return $item->setOrder(absint($number + $order));
            });
        endforeach;
    }

    /**
     * Initialisation du traitement de la soumission du formulaire.
     *
     * @return void
     */
    private function _initHandle()
    {
        $this->handle = new FormHandleController($this);
    }

    /**
     * Initialisation des messages de notification.
     *
     * @return void
     */
    private function _initNotices()
    {
        $this->notices = new FormNoticesController($this, $this->get('notices', []));
    }

    /**
     * Initialisation des options.
     *
     * @return void
     */
    private function _initOptions()
    {
        $this->options = new FormOptionsController($this, $this->get('options', []));
    }

    /**
     * Initialisation des données embarquées.
     *
     * @return void
     */
    private function _initTransport()
    {
        $this->transport = new FormTransportController($this);
    }

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
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut à récupérer.
     * @param mixed $defaul Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut à définir. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return void
     */
    public function set($key, $value)
    {
        return Arr::set($this->attributes, $key, $value);
    }

    /**
     * Récupération d'un addon actif
     *
     * @param string $name Nom de qualification de l'addon.
     *
     * @return null|AddonControllerInterface
     */
    public function getAddon($name)
    {
        if ($this->hasAddon($name)) :
            return $this->addons[$name];
        endif;
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
     * Récupération du nom de qualification du formulaire.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Récupération de la clé d'indexe de sécurisation du formulaire (CSRF).
     *
     * @return string
     */
    public function getNonce()
    {
        return '_' . $this->getUid() . '_nonce';
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
     * Récupération de la liste des options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options()->all();
    }

    /**
     * Récupération d'une option.
     *
     * @param string $key Clé d'indexe de l'attribut à récupérer. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return $this->options()->get($key, $default = null);
    }

    /**
     * Récupération de l'intitulé de qualification du formulaire.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title') ?: $this->getName();
    }

    /**
     * Récupération de l'identifiant unique de qualification du formulaire.
     *
     * @return string
     */
    public function getUid()
    {
        return $this->getPrefix() . $this->getName();
    }

    /**
     * Vérification d'existance d'un addon actif.
     *
     * @param string $name Nom de qualification de l'addon.
     *
     * @return bool
     */
    public function hasAddon($name)
    {
        return in_array($name, array_keys($this->addons));
    }

    /**
     * Evénement de déclenchement à l'initialisation du formulaire en tant que formulaire courant.
     *
     * @return void
     */
    public function onSetCurrent()
    {
        $this->call('form_set_current', [&$this]);
    }

    /**
     * Evénement de déclenchement à la réinitialisation du formulaire courant du formulaire.
     *
     * @return void
     */
    public function onResetCurrent()
    {
        $this->call('form_reset_current', [&$this]);
    }

    /**
     * Récupération des controleurs des addons actifs.
     *
     * @return AddonControllerInterface[]
     */
    public function addons()
    {
        return $this->addons;
    }

    /**
     * Récupération des controleurs des boutons actifs.
     *
     * @return ButtonControllerInterface[]
     */
    public function buttons()
    {
        return $this->buttons;
    }

    /**
     * Récupération du controleur des méthodes de rappel de court-circuitage.
     *
     * @return FormCallbacksController
     */
    public function callbacks()
    {
        return $this->callbacks;
    }

    /**
     * Récupération de la classe de rappel du controleur de surchage du formlaire
     *
     * @return FormBaseController
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * Affichage du formulaire.
     *
     * @return string
     */
    public function display()
    {
        return (string) new FormDisplayController($this);
    }

    /**
     * Récupération des controleurs de champs.
     *
     * @return FieldItemCollectionController
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Récupération du controleur de traitement de soumission de formulaire.
     *
     * @return FormHandleController
     */
    public function handle()
    {
        return $this->handle;
    }

    /**
     * Récupération du controleur de gestion des messages de notification.
     *
     * @return FormNoticesController
     */
    public function notices()
    {
        return $this->notices;
    }

    /**
     * Récupération du controleur de gestion des options.
     *
     * @return FormOptionsController
     */
    public function options()
    {
        return $this->options;
    }

    /**
     * Récupération du controleur de gestion des données embarquées.
     *
     * @return FormTransportController
     */
    public function transport()
    {
        return $this->transport;
    }
}