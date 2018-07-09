<?php

namespace tiFy\Field;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use tiFy\Apps\AppController;
use tiFy\Field\Field;
use tiFy\Field\FieldOptions\FieldOptionsCollectionController;
use tiFy\Field\TemplateController;
use tiFy\Kernel\Tools;

abstract class AbstractFieldItemController extends AppController implements FieldItemInterface
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
     * {@inheritdoc}
     */
    public function after()
    {
        $after = $this->get('after', '');

        echo is_callable($after) ? call_user_func($after) : $after;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function attrs()
    {
        echo $this->parseHtmlAttrs($this->get('attrs', []));
    }

    /**
     * {@inheritdoc}
     */
    public function before()
    {
        $before = $this->get('before', '');

        echo is_callable($before) ? call_user_func($before) : $before;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function content()
    {
        $content = $this->get('content', '');

        echo is_callable($content) ? call_user_func($content) : $content;
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        return $this->appTemplateRender($this->appLowerName(), $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttr($key, $default = '')
    {
        return Arr::get($this->attributes, "attrs.{$key}", $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttrs()
    {
        return Tools::Html()->parseAttrs($this->get('attrs', []), false);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get('name', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->appServiceGet('tify.field.item.field_options.collection.' . $this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->get('value', null);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttr($key)
    {
        return Arr::has($this->attributes, "attrs.{$key}");
    }

    /**
     * {@inheritdoc}
     */
    public function isChecked()
    {
        if (!$this->hasAttr('value')) :
            return false;
        endif;

        return $this->get('checked') === $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return array_keys($this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function options()
    {
        echo $this->appServiceGet('tify.field.item.field_options.collection.' . $this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        $this->parseName();
        $this->parseValue();
        $this->parseId();
        $this->parseClass();
        $this->parseTemplates();
    }

    /**
     * Traitement de l'attribut de configuration de l'attribut HTML "class".
     *
     * @return void
     */
    protected function parseClass()
    {
        $this->set(
            'attrs.class',
            sprintf($this->get('attrs.class', '%s'), "tiFyField-{$this->appShortname()}")
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
     * @return void
     */
    protected function parseId()
    {
        $this->set(
            'attrs.id',
            $this->get('attrs.id')
                ? : "tiFyField-{$this->appShortname()}--{$this->getIndex()}"
        );
    }

    /**
     * Traitement de l'attribut de configuration de la clé d'indexe de soumission du champ "name".
     *
     * @return void
     */
    protected function parseName()
    {
        if ($name = $this->get('name')) :
            $this->set('attrs.name', $name);
        endif;
    }

    /**
     * Traitement de l'attribut de configuration de la valeur de soumission du champ "value".
     *
     * @return array
     */
    protected function parseValue()
    {
        if ($value = $this->get('value')) :
            $this->set('attrs.value', $value);
        endif;
    }

    /**
     * Traitement de l'attribut de configuration de liste de selection "options".
     *
     * @return void
     */
    protected function parseOptions()
    {
        $optionsCollection = new FieldOptionsCollectionController($this->get('options', []));
        $this->appServiceAdd('tify.field.item.field_options.collection.' . $this->getId(), $optionsCollection);

        $optionsCollection->init();
        foreach($optionsCollection as $item) :
            if (!$item->isGroup() && in_array($item->getValue(), $this->getValue(), true)) :
                $item->push('selected', 'attrs');
            endif;
        endforeach;
    }

    /**
     * Traitement des l'attributs de configuration du controleur de templates.
     *
     * @param array $attrs {
     *      Liste des attributs de template personnalisés.
     *
     *      @var string $basedir Répertoire de stockage des templates.
     *      @var string|callable Classe de rappel du controleur de template.
     *      @var array $args Liste des variables d'environnement passée en argument.
     * }
     * @return array
     */
    protected function parseTemplates($attrs = [])
    {
        $this->set(
            'templates',
            array_merge(
                [
                    'basedir'    => get_template_directory() . '/templates/presstify/field/' . $this->appLowerName(),
                    'controller' => TemplateController::class
                ],
                $attrs ? : $this->get('templates', [])
            )
        );

        $this->appTemplates($this->get('templates'));
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function values()
    {
        return array_values($this->attributes);
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->display();
    }
}