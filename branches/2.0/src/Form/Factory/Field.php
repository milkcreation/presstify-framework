<?php

namespace tiFy\Form\Factory;

use Illuminate\Support\Arr;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Kernel\Parameters\ParamsBagController;
use tiFy\Form\Factory\ResolverTrait as FormFactoryResolver;

class Field extends ParamsBagController implements FactoryField
{
    use FormFactoryResolver;

    /**
     * Liste des attributs de configuration.
     * @var array  {
     *
     * @var string $title Intitulé de qualification. Valeur par défaut. ex. label.
     * @var string $before Contenu HTML affiché avant le champ.
     * @var string $after Contenu HTML affiché après le champ.
     * @var bool|string|array $wrapper Affichage de l'encapuleur de champ. false si masqué|true charge les attributs par défaut|array permet de définir des attributs personnalisés.
     * @var bool|string|array $label Affichage de l'intitulé de champ. false si masqué|true charge les attributs par défaut|array permet de définir des attributs personnalisés.
     * @var array $support Définition des attributs de support. label|wrapper|request|tabindex.
     * @var int $group Indice du groupe d'appartenance.
     * @var int $order Ordre d'affichage général ou dans le groupe s'il est défini.
     * @var string $type Type de champ.
     * @var string $name Indice de qualification de la variable de requête.
     * @var mixed $value Valeur courante de la variable de requête.
     * @var array $choices Liste de choix des valeurs multiples.
     * @var array $attrs Listes des attributs HTML. (hors name & value)
     * @var array $extras Listes des attributs de configuration de champ complémentaire.
     * @var boolean|string|array $required Configuration de champs requis. false si désactivé|true charge les attributs par défaut| array {
     *
     *      @var boolean|string|array $tagged Affichage de l'indicateur de champ requis. false si masqué|true charge les attributs par défaut|string valeur de l'indicateur|array permet de définir des attributs personnalisés.
     *      @var boolean $check Activation du test d'existance natif.
     *      @var mixed $value_none Valeur à comparer pour le test d'existance.
     *      @var string|callable $call Fonction de validation ou alias de qualification.
     *      @var array $args Liste des variables passées en argument dans la fonction de validation.
     *      @var string $message Message de notification en cas d'erreur.
     * }
     * @var array $validation {
     *      Liste des fonctions de validation d'intégrité du champ lors de la soumission.
     *
     *      @var string|callable $call Fonction de validation ou alias de qualification.
     *      @var array $args Liste des variables passées en arguments dans la fonction de validation.
     *      @var string $message Message de notification d'erreur.
     * }
     */
    protected $attributes = [
        'title'           => '',
        'before'          => '',
        'after'           => '',
        'wrapper'         => true,
        'label'           => true,
        'support'         => [],
        'group'           => 0,
        'position'        => 0,
        'type'            => 'html',
        'name'            => '',
        'value'           => '',
        'choices'         => [],
        'attrs'           => [],
        'extras'          => [],
        'required'        => false,
        'validation'      => [],
        //@todo 'transport'       => true,
        //@todo 'addons'          => []
    ];

    /**
     * Identifiant de qualification du champ.
     * @var string
     */
    protected $slug = '';


    /**
     * -----------------------------------------------------------------------------------------------------------------
     */
    /**
     * Attributs de test d'intégrité de champ requis.
     * @var false|array
     */
    protected $required = false;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param FormFactory $form Instance du contrôleur de formulaire.
     *
     * @return void
     */
    public function __construct($slug, $attrs = [], FormFactory $form)
    {
        $this->slug = $slug;
        $this->form = $form;

        // A l'issue du chargement complet de la liste des champs.
        $this->events('field.init', [&$this]);

        // A l'issue du chargement complet de la liste des champs.
        $this->events()->listen(
            'fields.init',
            function () use ($attrs) {
                $this->parse($attrs);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getController();
    }

    public function getController()
    {
        return app("form.field.controller.{$this->form->name()}.{$this->getSlug()}");
    }

    /**
     * {@inheritdoc}
     */
    public function getExtras($key = null, $default = null)
    {
        return (is_null($key)) ? $this->get('extras', []) : $this->get("extras.{$key}", $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return $this->get('group', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->get('position', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->get('title') ? : $this->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($raw = false)
    {
        $value = $this->get('value');

        $this->events('field.get.value', [&$value, $this]);

        if ($raw) :
            $value = is_array($value) ? array_map('esc_attr', $value) : esc_attr($value);
        endif;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues($raw = false, $glue = ', ')
    {
        $value = Arr::wrap($this->getValue(false));

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

        if (!is_null($glue)) :
            $value = join($glue, $value);
        endif;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLabel()
    {
        return !empty($this->get('label'));
    }

    /**
     * {@inheritdoc}
     */
    public function hasSupport($support)
    {
        return in_array($support, $this->get('support', []));
    }

    /**
     * {@inheritdoc}
     */
    public function hasWrapper()
    {
        return !empty($this->get('wrapper'));
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->has('attrs.id', '')) :
            $this->set('attrs.id', "Form-fieldInput--{$this->getSlug()}");
        endif;
        if (!$this->get('attrs.id')) :
            $this->pull('attrs.id');
        endif;

        $default_class = "Form-fieldInput Form-fieldInput--{$this->getType()} Form-fieldInput--{$this->getSlug()}";
        if (!$this->has('attrs.class')) :
            $this->set('attrs.class', $default_class);
        else :
            $this->set('attrs.class', sprintf($this->get('attrs.class', ''), $default_class));
        endif;
        if (!$this->get('attrs.class')) :
            $this->pull('attrs.class');
        endif;

        if (!$this->has('attrs.tabindex', 0)) :
            $this->set('attrs.tabindex', $this->getPosition());
        endif;
        if ($this->get('attrs.tabindex') === false) :
            $this->pull('attrs.tabindex');
        endif;

        if ($wrapper = $this->get('wrapper')) :
            $wrapper = (is_array($wrapper)) ? $wrapper : [];
            $this->set('wrapper', array_merge(['tag' => 'div', 'attrs' => []], $wrapper));

            if (!$this->has('wrapper.attrs.id', '')) :
                $this->set('wrapper.attrs.id', "Form-field--{$this->getSlug()}");
            endif;
            if (!$this->get('wrapper.attrs.id')) :
                $this->pull('wrapper.attrs.id');
            endif;

            $default_class = "Form-field Form-field--{$this->getType()} Form-field--{$this->getSlug()}";
            if (!$this->has('wrapper.attrs.class')) :
                $this->set('wrapper.attrs.class', $default_class);
            else :
                $this->set('wrapper.attrs.class', sprintf($this->get('wrapper.attrs.class', ''), $default_class));
            endif;
            if (!$this->get('wrapper.attrs.class')) :
                $this->pull('wrapper.attrs.class');
            endif;
        endif;

        if ($required = $this->get('required', false)) :
            $required = (is_array($required))
                ? $required
                : (is_string($required) ? ['message' => $required] : []);

            $required = array_merge(
                [
                    'tagged'     => true,
                    'check'      => true,
                    'value_none' => '',
                    'call'       => '',
                    'args'       => [],
                    'message'    => __('Le champ "%s" doit être renseigné.', 'tify'),
                    'html5'      => false,
                ],
                $required
            );

            if ($tagged = $required['tagged']) :
                $tagged = is_array($tagged)
                    ? $tagged
                    : (is_string($tagged)) ? ['content' => $tagged] : [];
                $required['tagged'] = array_merge(
                    [
                        'tag'     => 'span',
                        'attrs'   => [],
                        'content' => '*'
                    ],
                    $tagged
                );
            endif;

            $required['call'] = isset($required['value_none']) && empty($required['call'])
                ? 'is_diff'
                : 'is_empty';
            $required['args'] = isset($required['value_none']) && empty($required['args'])
                ? [] + [$required['value_none']]
                : [];

            $this->set('required', $required);

            if ($this->get('required.tagged')) :
                if (!$this->has('required.tagged.attrs.id', '')) :
                    $this->set('required.tagged.attrs.id', "Form-fieldTag--{$this->getSlug()}");
                endif;
                if (!$this->get('required.tagged.attrs.id')) :
                    $this->pull('required.tagged.attrs.id');
                endif;

                $default_class = "Form-fieldTag Form-fieldTag--{$this->getType()} Form-fieldTag--{$this->getSlug()}";
                if (!$this->has('required.tagged.attrs.class')) :
                    $this->set('required.tagged.attrs.class', $default_class);
                else :
                    $this->set('required.tagged.attrs.class', sprintf($this->get('required.tagged.attrs.class', ''), $default_class));
                endif;
                if (!$this->get('required.tagged.attrs.class')) :
                    $this->pull('required.tagged.attrs.class');
                endif;
            endif;
        endif;

        if ($label = $this->get('label')) :
            $label = (is_array($label)) ? $label : [];
            $this->set('label', array_merge(['tag' => 'label', 'attrs' => []], $label));

            if (!$this->has('label.attrs.id', '')) :
                $this->set('label.attrs.id', "Form-fieldLabel--{$this->getSlug()}");
            endif;
            if (!$this->get('label.attrs.id')) :
                $this->pull('label.attrs.id');
            endif;

            $default_class = "Form-fieldLabel Form-fieldLabel--{$this->getType()} Form-fieldLabel--{$this->getSlug()}";
            if (!$this->has('label.attrs.class')) :
                $this->set('label.attrs.class', $default_class);
            else :
                $this->set('label.attrs.class', sprintf($this->get('label.attrs.class', ''), $default_class));
            endif;
            if (!$this->get('label.attrs.class')) :
                $this->pull('label.attrs.class');
            endif;

            if ($for = $this->get('attrs.id')) :
                $this->set('label.attrs.for', $for);
            endif;

            if (!$this->has('label.content', '')) :
                $this->set('label.content', $this->getTitle());
            endif;
            if (!$this->get('label.content')) :
                $this->pull('label.content');
            endif;
        endif;

        app()->singleton(
            "form.field.controller.{$this->form()->name()}.{$this->getSlug()}",
            function ($name, FactoryField $field) {
                if (app()->bound("form.field.{$this->getType()}")) :
                    return app("form.field.{$this->getType()}", [$field]);
                else :
                    return app("form.field.defaults", [$name, $field]);
                endif;
            }
        )->build([$this->getType(), $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtra($key, $value)
    {
        return $this->set("extras.{$key}", $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position = 0)
    {
        $this->set('position', $position);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSupport($key, $value)
    {
        $this->set("support.{$key}", $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->set('value', $value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     */
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
}