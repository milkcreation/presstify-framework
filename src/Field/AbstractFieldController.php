<?php

namespace tiFy\Field;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\Apps\AppController;
use tiFy\Field\Field;
use tiFy\Field\TemplateController;
use tiFy\Kernel\Tools;

abstract class AbstractFieldController extends AppController
{
    /**
     * Identifiant de qualification du champ.
     * @var string
     */
    protected $id = '';

    /**
     * Compte de l'indice de l'instance courante.
     * @var int
     */
    protected $index = 0;

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * Court-circuitage de l'intanciation.
     *
     * @return void
     */
    private function __construct()
    {
        $field = $this->appServiceGet(Field::class);

        if (! $field->existsInstance(get_called_class())) :
            $field->setInstance(get_called_class(), Str::random(32), $this);
            $this->boot();
        endif;
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __wakeup()
    {

    }

    /**
     * Instanciation.
     *
     * @param string $id Identifiant de qualification du controleur (Optionel).
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return $this
     */
    public function __invoke($id = null, $attrs = [])
    {
        if (is_null($id)) :
            $id = Str::random(32);
        elseif(is_array($id)) :
            $attrs = $id;
            $id = Str::random(32);
        endif;

        $field = $this->appServiceGet(Field::class);
        if (!$instance = $field->getInstance(get_called_class(), $id)) :
            $instance = $this;
            $count = $field->countInstance(get_called_class());
            $this->id = $id;
            $this->index = $count++;
            $this->parse($attrs);

            $field->setInstance(get_called_class(), $instance->getId(), $instance);
        endif;

        return $instance;
    }

    /**
     * Création d'une instance du controleur.
     *
     * @return static
     */
    final public static function make()
    {
        return new static();
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    final protected function boot()
    {
        if (method_exists($this, 'init')) :
            $this->appAddAction('init');
        endif;
    }

    /**
     * Affichage du contenu placé après le champ
     *
     * @return void
     */
    public function after()
    {
        echo $this->get('after', '');
    }

    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Affichage de la liste des attributs de balise.
     *
     * @return string
     */
    public function attrs()
    {
        echo $this->parseHtmlAttrs($this->get('attrs', []));
    }

    /**
     * Affichage du contenu placé avant le champ
     *
     * @return void
     */
    public function before()
    {
        echo $this->get('before', '');
    }

    /**
     * Récupération une liste d'attributs de configuration.
     *
     * @param string[] $keys Clé d'index des attributs à retourner.
     *
     * @return array
     */
    public function compact($keys = [])
    {
        if (empty($keys)) :
            return $this->all();
        endif;

        $attrs = [];
        foreach ($keys as $key) :
            $attrs[$key] = $this->get($key);
        endforeach;

        return $attrs;
    }

    /**
     * Affichage du contenu de la balise champ
     *
     * @return void
     */
    public function content()
    {
        echo $this->get('content', '');
    }

    /**
     * Affichage.
     *
     * @return string
     */
    abstract protected function display();

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Récupération d'un attribut de balise HTML.
     *
     * @param string $key Clé d'indexe de l'attribut.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function getAttr($key, $default = '')
    {
        return Arr::get($this->attributes, "attrs.{$key}", $default);
    }

    /**
     * Récupération de la liste des attributs HTML.
     *
     * @return array
     */
    public function getAttrs()
    {
        return Tools::Html()->parseAttrs($this->get('attrs', []), false);
    }

    /**
     * Récupération de l'identifiant de qualification du controleur.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Récupération de l'indice de la classe courante.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Récupération de l'attribut de configuration de la qualification de soumission du champ "name"
     *
     * @return string
     */
    public function getName()
    {
        return $this->get('name', '');
    }

    /**
     * Récupération des attributs d'une option de liste de sélection selon sa valeur
     *
     * @param mixed $value Valeur de l'option à récupérer
     *
     * @return null|array
     */
    public function getOption($value)
    {
        if (!$options = $this->getOptions()) :
            return null;
        endif;

        foreach($options as $option) :
            if ($option['value'] == $value) :
                return $option;
            endif;
        endforeach;

        return null;
    }

    /**
     * Récupération des attributs des options de liste de sélection
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->get('options', []);
    }

    /**
     * Récupération des attributs des options de liste de sélection
     *
     * @return string[]
     */
    public function getOptionValues()
    {
        if (!$options = $this->getOptions()) :
            return [];
        endif;

        return array_column($options, 'value');
    }

    /**
     * Récupération de l'attribut de configuration de la valeur initiale de soumission du champ "value".
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->get('value', null);
    }

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * Vérification d'existance d'un attribut de balise HTML.
     *
     * @param string $key Clé d'indexe de l'attribut.
     *
     * @return string
     */
    public function hasAttr($key)
    {
        return Arr::has($this->attributes, "attrs.{$key}");
    }

    /**
     * Vérification de correspondance entre la valeur de coche et celle du champ.
     *
     * @return bool
     */
    public function isChecked()
    {
        if (!$this->hasAttr('value')) :
            return false;
        endif;

        return $this->get('checked') === $this->getValue();
    }

    /**
     * Récupération de la liste des clés d'indexes des attributs de configuration.
     *
     * @return string[]
     */
    public function keys()
    {
        return array_keys($this->attributes);
    }

    /**
     * Affichage du contenu de la liste de selection
     *
     * @return void
     */
    public function options()
    {
        $selected = $this->getValue();

        $options = array_map(
            function($item) use ($selected) {
                if (in_array($item['value'],$selected)) :
                    $item['attrs'][] = 'selected';
                endif;

                return $item;
            },
            $this->get('options', [])
        );

        echo WalkerOptions::display($options);
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return void
     */
    protected function parse($attrs = [])
    {
        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        $this->parseName($attrs);
        $this->parseValue($attrs);
        $this->parseId($attrs);
        $this->parseClass($attrs);
        $this->parseTemplates($attrs);
        $this->parseOptions($attrs);
    }

    /**
     * Traitement de l'attribut de configuration de l'attribut HTML "class".
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return void
     */
    protected function parseClass($attrs = [])
    {
        $this->set(
            'attrs.class',
            sprintf(Arr::get($attrs, 'attrs.class', '%s'), "tiFyField-{$this->appShortname()}")
        );
    }

    /**
     * Traitement d'une liste d'attributs HTML.
     *
     * @param array $attrs Liste des attributs HTML.
     * @param bool $linearized Activation de la linéarisation.
     *
     * @return string
     */
    protected function parseHtmlAttrs($attrs = [], $linearized = true)
    {
        return Tools::Html()->parseAttrs($attrs, $linearized);
    }

    /**
     * Traitement de l'attribut de configuration de l'attribut HTML "id".
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return void
     */
    protected function parseId($attrs = [])
    {
        $this->set(
            'attrs.id',
            Arr::get($attrs, 'attrs.id')
                ? : "tiFyField-{$this->appShortname()}--{$this->getIndex()}"
        );
    }

    /**
     * Traitement de l'attribut de configuration de la clé d'indexe de soumission du champ "name".
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return void
     */
    protected function parseName($attrs = [])
    {
        if ($name = Arr::get($attrs, 'name')) :
            $this->set('attrs.name', $name);
        endif;
    }

    /**
     * Traitement de l'attribut de configuration de la valeur de soumission du champ "value".
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parseValue($attrs = [])
    {
        if ($value = Arr::get($attrs, 'value')) :
            $this->set('attrs.value', $value);
        endif;
    }

    /**
     * Traitement de l'attribut de configuration de liste de selection "options".
     *
     * @param array $args Liste des attributs de configuration
     *
     * @return void
     */
    protected function parseOptions($args = [])
    {
        if (!isset($args['options'])) :
            return $args;
        endif;

        if (!is_array($args['options'])) :
            $options = array_map('trim', explode(',', (string)$args['options']));
        else:
            $options = $args['options'];
        endif;

        $_options = [];
        $i = 0;
        foreach($options as $k => $v) :
            if (is_numeric($k)) :
                if (!is_array($v)) :
                    $v = [
                        'label' => $v,
                        'value' => $k
                    ];
                else :
                    if (!isset($v['value'])) :
                        $v['value'] = $k;
                    endif;
                endif;
            else :
                $v = [
                    'label' => $v,
                    'value' => $k
                ];
            endif;
            $option = array_merge(
                [
                    'name'     => $i++,
                    'group'  => false,
                    'attrs'  => [],
                    'parent' => ''
                ],
                $v
            );

            // Formatage des attributs
            if (!isset($option['label'])) :
                $option['label'] = $option['value'];
            endif;

            $_options[] = $option;
        endforeach;

        $this->attributes['options'] = $_options;
    }

    /**
     * Traitement des l'attributs de configuration du controleur de templates.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parseTemplates($attrs = [])
    {
        $this->set(
            'templates',
            array_merge(
                [
                    'basedir'    => get_template_directory() . '/templates/presstify/field/' . $this->appLowerName(),
                    'controller' => TemplateController::class,
                    'args'       => []
                ],
                Arr::get($attrs, 'templates', [])
            )
        );
        $this->set(
            'templates.args',
            array_merge(
                [
                    'id'    => $this->id,
                    'index' => $this->index
                ],
                $this->get('templates.args', [])
            )
        );

        $this->appTemplates($this->get('templates'));
    }

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return $this
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * Définition d'un attribut de balise HTML
     *
     * @param string $key Clé d'indexe de l'attribut.
     * @param null|mixed $value Valeur de l'attribut.
     *
     * @return $this
     */
    public function setAttr($key, $value = null)
    {
        if(is_null($value)) :
            Arr::set($this->attributes, 'attrs', $this->get('attrs', [])+[$key]);
        else :
            Arr::set($this->attributes, "attrs.{$key}", $value);
        endif;

        return $this;
    }

    /**
     * Récupération de la liste des valeurs des attributs de configuration.
     *
     * @return mixed[]
     */
    public function values()
    {
        return array_values($this->attributes);
    }

    /**
     * Récupération de l'affichage du controleur depuis l'instance.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->display();
    }
}