<?php

namespace tiFy\Form\Fields;

use Illuminate\Support\Arr;
use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Fields\AbstractFieldTypeController;
use tiFy\Form\Fields\FieldDisplayController;
use tiFy\Form\Forms\FormItemController;

class FieldItemController extends AbstractCommonDependency
{
    /**
     * Compteur d'instance du champ de formulaire.
     * @var int[]
     */
    protected static $instance = [];

    /**
     * Compteur d'indexation.
     * @var int
     */
    protected $index = 0;

    /**
     * Liste des attributs de configuration.
     * @var array  {
     *
     * @var string $slug Nom de qualification du champ.
     * @var string $title Intitulé de qualification. Valeur par défaut. ex. label.
     * @var string $before Contenu HTML affiché avant le champ.
     * @var string $after Contenu HTML affiché après le champ.
     * @var bool|string|array $wrapper Affichage de l'encapuleur de champ. false > masqué|true > attributs par défaut|array > Liste des attributs.
     * @var bool|string|array $label Affichage de l'intitulé de champ. false > masqué|true > attributs par défaut|array > Liste des attributs.
     * @var array $support Court-circuitage des propriétés de champs supportées. label|wrapper|request.
     * @var int $group Groupe d'appartenance du champ.
     * @var int $order Ordre d'affichage du champ.
     * @var string $type Type de champ.
     * @var string $name Indice de qualification de la variable de requête.
     * @var mixed $value Valeur courante de la variable de requête.
     * @var array $choices Liste de choix des valeurs multiples.
     * @var array $attrs Listes des attributs HTML complémentaires (hors 'name' et 'value')
     * @var bool|string|array $required {
     *      Liste des attributs de configuration du champ requis.
     *
     *      @var bool|string $tag Affichage de l'indicateur de champ requis
     *      @var bool $check Activation du test d'existance natif.
     *      @var mixed $value_none Valeur à comparer pour le test d'existance.
     *      @var string|callable $cb Fonction de rappel.
     *      @var array $args Liste des variables passées en argument dans la fonction de rappel.
     *      @var string $message Message de notification en cas d'erreur.
     *      @var bool $html5 Utilisation du court-circuitage HTML5.     *
     * }
     * @var string|array $integrity_cb {
     *      Liste des fonctions de test d'intégrité du champ lors de la soumission.
     *
     *      @var string|callable $cb Intitulé d'alias de vérification @see \tiFy\Components\Tools\Checker\CheckerTrait ou Fonction de rappel personnalisée.
     *      @var array $args Liste des variables passées en arguments dans la fonction de rappel.
     *      @var string $message Message de notification d'erreur.
     * }
     */
    protected $attributes = [
        'slug'            => '',
        'title'           => '',
        'before'          => '',
        'after'           => '',
        'wrapper'         => true,
        'label'           => true,
        'support'         => [],
        'group'           => 0,
        'order'           => 0,
        'type'            => 'html',
        'name'            => '',
        'value'           => '',
        'choices'         => [],
        'attrs'           => [],
        'required'        => false,
        'integrity_cb'    => false,
        'transport'       => true,
        'addons'          => [],
        'options'         => [],
    ];

    /**
     * Valeur courante de la variable de requête.
     * @var mixed
     */
    protected $value = '';

    /**
     * Attributs de test d'intégrité de champ requis.
     * @var false|array
     */
    protected $required = false;

    /**
     * Liste des tests d'intégrité.
     * @var array
     */
    protected $integrityCallbacks = [];

    /**
     * Ordre d'affichage dans le groupe.
     * @var int
     */
    protected $order = 0;

    /**
     * Classe de rappel du controleur de type de champ.
     * @var AbstractFieldTypeController
     */
    protected $fieldTypeController;

    /**
     * Indice de tabulation.
     * @return int
     */
    protected static $tabIndex = 0;

    /**
     * CONSTRUCTEUR.
     *
     * @param Form $Form Classe de rappel du controleur de formulaire associé.
     * @param array $attrs Liste des attributs de configuration du champ.
     *
     * @return void
     */
    public function __construct(FormItemController $form, $attrs = [])
    {
        parent::__construct($form);

        $this->_initIndex();

        $this->_initAttributes($attrs);

        $this->_initFieldTypeController();

        $this->_initAddonsOptions();

        $this->_initValue();

        $this->_initCheckIntegrity();

        // Court-circuitage des paramètre du champ
        $this->call('field_init_params', [&$this]);
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {

    }

    /**
     * Initialisation de la liste des attributs de configuration du champ des addons associés au formulaire.
     *
     * @return void
     */
    private function _initAddonsOptions()
    {
        if($addons = $this->getAddons()) :
            foreach ($this->getAddons() as $name => $addon) :
                $addon->parseDefaultFieldOptions($this);
            endforeach;
        endif;
    }

    /**
     * Initialisation de la liste des attributs de configuration.
     *
     * @return void
     */
    private function _initAttributes($attrs)
    {
        $this->attributes = $this->recursiveParseArgs($attrs, $this->attributes);

        if (empty($this->attributes['slug'])) :
            $this->attributes['slug'] = 'field-slug_' . $this->getForm()->getUid() . '-' . $this->getIndex();
        endif;

        if (empty($this->attributes['name'])) :
            $this->attributes['name'] = $this->attributes['slug'];
        endif;
    }

    /**
     * Initialisation des test d'intégrité lors de la soumission
     *
     * @return void
     */
    private function _initCheckIntegrity()
    {
        if ($required = $this->get('required', false)) :
            $defaults = [
                'tag'        => true,
                'check'      => true,
                'value_none' => '',
                'cb'         => isset($required['value_none']) ? 'is_diff' : 'is_empty',
                'args'       => isset($required['value_none']) ? [] + [$required['value_none']] : [],
                'message'    => __('Le champ "%s" doit être renseigné.', 'tify'),
                'html5'      => false,
            ];

            if (is_bool($required)) :
                $required = array_merge(
                    $defaults,
                    [
                        'tag'   => true,
                        'check' => true,
                    ]
                );
            elseif (is_string($required)) :
                $required = array_merge(
                    $defaults,
                    [
                        'tag'     => true,
                        'check'   => true,
                        'message' => $required,
                    ]
                );
            else :
                $required = array_merge($defaults, $required);
            endif;
        endif;
        $this->required = $required;

        if ($integrity_cb = $this->get('integrity_cb', false)) :
            $this->_recursiveParseIntegrityCallbacks($integrity_cb);
        endif;
    }

    /**
     * Traitement récursif des tests d'intégrités a passer lors de la soumission du formulaire.
     *
     * @return void
     */
    private function _recursiveParseIntegrityCallbacks($integrity_cb)
    {
        $defaults = [
            'cb'        => '__return_true',
            'args'      => [],
            'message'   => __('Le format du champ "%s" est invalide', 'tify'),
        ];

        if (is_string($integrity_cb)) :
            $integrity_cb = array_map('trim', explode(',', $integrity_cb));

            foreach ($integrity_cb as $cb) :
                $this->integrityCallbacks[] = array_merge(
                    $defaults,
                    ['cb' => $cb]
                );
            endforeach;
        elseif (is_array($integrity_cb)) :
            if (isset($integrity_cb['cb'])) :
                $this->integrityCallbacks[] = array_merge(
                    $defaults,
                    $integrity_cb
                );
            else :
                foreach($integrity_cb as $cb) :
                    $this->_recursiveParseintegrityCallbacks($cb);
                endforeach;
            endif;
        endif;
    }

    /**
     * Initialisation du controleur de type de champ.
     *
     * @return void
     */
    private function _initFieldTypeController()
    {
        $this->fieldTypeController = $this->appServiceGet(FieldTypesController::class)->set($this->get('type'), $this);
    }

    /**
     * Initialisation de l'indexation.
     *
     * @return void
     */
    private function _initIndex()
    {
        if (!isset(self::$instance[$this->getForm()->getName()])) :
            $this->index = self::$instance[$this->getForm()->getName()] = 0;
        else :
            $this->index = self::$instance[$this->getForm()->getName()]++;
        endif;
    }

    /**
     * Initialisation de la valeur courante de la variable de requête.
     *
     * @return void
     */
    private function _initValue()
    {
        $value = $this->get('value', '');

        // Court-circuitage de l'initialisation de la valeur du champ
        $this->call('field_init_value', [&$value, $this]);

        $this->value = $value;
    }

    /**
     * Traitement de la liste des options par défaut d'un addon.
     *
     * @param string $name Nom de qualification de l'addon.
     * @param array $defaults Liste des options par défaut de champs.
     *
     * @return void
     */
    public function parseDefaultAddonOptions($name, $defaults = [])
    {
        $this->set(
            "addons.{$name}",
            $this->recursiveParseArgs($this->getAddonOptions($name), $defaults)
        );
    }

    /**
     * Traitement de la liste des options.
     *
     * @param array $defaults Liste des options par défaut.
     *
     * @return void
     */
    public function parseDefaultOptions($defaults = [])
    {
        $this->set(
            'options',
            $this->recursiveParseArgs($this->getOptions(), $defaults)
        );
    }

    /**
     * Traitement de la liste des propriétes de champ supportées.
     *
     * @param array $defaults Liste des options par défaut.
     *
     * @return void
     */
    public function parseDefaultSupport($defaults = [])
    {
        if (!$this->get('support')) :
            $this->set(
                'support',
                $defaults
            );
        endif;
    }

    /**
     * Traitement de la liste des options.
     *
     * @param array $defaults Liste des attributs de balise HTML par défaut.
     *
     * @return void
     */
    public function parseDefaultHtmlAttrs($defaults = [])
    {
        $this->set(
            'attrs',
            $this->recursiveParseArgs($this->getHtmlAttrs(), $defaults)
        );

        $this->setHtmlAttr(
            'id',
            sprintf(
                $this->getHtmlAttr('id', '%s'),
                'tiFyForm-FieldInput--' . $this->getForm()->getName() .
                '_' . $this->getSlug()
            )
        );

        $this->setHtmlAttr(
            'class',
            sprintf(
                $this->getHtmlAttr('class', '%s'),
                'tiFyForm-FieldInput' .
                ' tiFyForm-FieldInput--' . $this->get('type') .
                ' tiFyForm-FieldInput--' . $this->getName() .
                ' tiFyForm-FieldInput--' . $this->getSlug()
            )
        );

        $this->setHtmlAttr(
            'tabindex',
             $this->getTabIndex()
        );
    }

    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string Clé d'indexe de l'attribut à récupérer.
     * @param mixed $default Valeur de retour par défaut.
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
     * @param string Clé d'indexe de l'attribut à définir.
     * @param mixed Valeur de l'attribut à définir.
     *
     * @return void
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);
    }

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string Clé d'indexe de l'attribut à vérifier.
     *
     * @return void
     */
    public function has($key, $value)
    {
        return Arr::has($this->attributes, $key);
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
     * Récupération de la classe de rappel du controleur de type de champ.
     *
     * @return AbstractFieldTypeController
     */
    public function getFieldTypeController()
    {
        return $this->fieldTypeController;
    }

    /**
     * Récupération du groupe d'appartenance.
     *
     * @return int
     */
    public function getGroup()
    {
        return $this->get('group', 0);
    }

    /**
     * Récupération de la liste des attributs de balise HTML.
     *
     * @return array
     */
    public function getHtmlAttrs()
    {
        return $this->get('attrs', []);
    }

    /**
     * Récupération d'un attribut de balise HTML.
     *
     * @param string $key Clé d'indexe de l'attribut à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return array
     */
    public function getHtmlAttr($key, $default = '')
    {
        return $this->get("attrs.{$key}", $default);
    }

    /**
     * Définition d'un attribut de balise HTML.
     *
     * @param string $key Clé d'indexe de l'attribut à définir
     * @param mixed $default Valeur de l'attribut.
     *
     * @return array
     */
    public function setHtmlAttr($key, $value)
    {
        return $this->set("attrs.{$key}", $value);
    }

    /**
     * Récupération de l'index.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Récupération de la liste des tests d'intégrité à traiter au moment de la soumission du formulaire.
     *
     * @return array
     */
    public function getIntegrityCallbacks()
    {
        return $this->integrityCallbacks;
    }

    /**
     * Récupération de l'indice de qualification de la variable de requête.
     *
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * Récupération de l'ordre d'affichage du champ.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Définition de l'ordre d'affichage du champ.
     *
     * @param int $order Définition de l'ordre d'affichage du champ
     *
     * @return int
     */
    public function setOrder($order = 0)
    {
        $this->order = $order;
    }

    /**
     * Récupération de la liste des options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->get('options', []);
    }

    /**
     * Récupération d'une option.
     *
     * @param string $key Clé d'indexe de l'option à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return array
     */
    public function getOption($key, $default = '')
    {
        return $this->get("options.{$key}", []);
    }

    /**
     * Définition d'une option.
     *
     * @param string $key Clé d'indexe de l'option à définir
     * @param mixed $default Valeur de l'option.
     *
     * @return array
     */
    public function setOption($key, $value)
    {
        return $this->set("options.{$key}", $value);
    }

    /**
     * Récupération des attributs de test d'intégrité de champ requis.
     *
     * @return array
     */
    public function getRequiredAll()
    {
        return $this->required ? : [];
    }

    /**
     * Récupération d'un attribut de test d'intégrité de champ requis.
     *
     * @param string Clé d'indexe de l'attribut à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return array
     */
    public function getRequiredAttr($key, $default = '')
    {
        return Arr::get($this->required, $key, $default);
    }

    /**
     * Vérifie si un champs est requis.
     *
     * @return bool
     */
    public function isRequired()
    {
        return !empty($this->required);
    }

    /**
     * Vérification d'une propriété de support.
     *
     * @param string $prop Propriété du support à vérifier.
     *
     * @return array
     */
    public function support($prop)
    {
        return in_array($prop, $this->get('support', []));
    }

    /**
     * Définition d'une propriété de support.
     *
     * @param string $key Clé d'indexe du support à définir
     * @param mixed $default Valeur du support.
     *
     * @return array
     */
    public function setSupport($key, $value)
    {
        return $this->set("support.{$key}", $value);
    }

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->get('slug');
    }

    /**
     * Récupération de l'indice de tabulation.
     *
     * @return int
     */
    public function getTabIndex()
    {
        if ($tabindex = $this->get('tabindex')) :
            return (int) $tabindex;
        else :
            return ++self::$tabIndex;
        endif;
    }

    /**
     * Récupération de l'intitulé de qualification
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title') ? : $this->getSlug();
    }

    /**
     * Récupération de la valeur du champ
     *
     * @param bool Retour de la valeur brute.
     *
     * @return mixed
     */
    public function getValue($raw = false)
    {
        $value = $this->value;

        // Court-circuitage de la récupération de la valeur du champ.
        $this->call('field_get_value', [&$value, $this]);

        // Sécurisation de la récupération de la valeur
        if (! $raw) :
            $value = is_array($value) ? array_map('esc_attr', $value) : esc_attr($value);
        endif;

        return $value;
    }

    /**
     * Récupération de la valeur d'affichage du champ.
     *
     * @param bool Retour de la valeur brute.
     * @param null|string $join Caractère d'assemblage de la valeur.
     *
     * @return string
     */
    public function getValueDisplay($raw = false, $join = ', ')
    {
        $value = (array)$this->getValue();

        if ($choices = $this->get('choices', [])) :
            foreach ($value as &$v) :
                if (isset($choices[$v])) :
                    $v = $choices[$v];
                endif;
            endforeach;
        endif;

        if ($raw) :
            $value = is_array($value) ? array_map('esc_attr', $value) : esc_attr($value);
        endif;

        if (!is_null($join)) :
            $value = join($join, $value);
        endif;

        return $value;
    }

    /**
     * Définition de la valeur d'un champ.
     *
     * @param mixed $value Valeur à définir.
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Affichage du champs.
     *
     * @return string
     */
    public function display()
    {
        return new FieldDisplayController($this);
    }
}