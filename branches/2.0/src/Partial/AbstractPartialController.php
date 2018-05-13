<?php

namespace tiFy\Partial;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\Apps\AppController;
use tiFy\Partial\Partial;

abstract class AbstractPartialController extends AppController
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
        $partial = $this->appServiceGet(Partial::class);

        if (! $partial->existsInstance(__CLASS__)) :
            $partial->setInstance(__CLASS__, Str::random(32), $this);
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

        $partial = $this->appServiceGet(Partial::class);
        if (!$instance = $partial->getInstance(__CLASS__, $id)) :
            $instance = $this;
            $count = $partial->countInstance(__CLASS__);
            $this->id = $id;
            $this->index = $count++;
            $this->parse($attrs);

            $partial->setInstance(__CLASS__, $instance->getId(), $instance);
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
    protected function boot()
    {
        if (method_exists($this, 'init')) :
            $this->appAddAction('init');
        endif;
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
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }

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
     * Récupération de la liste des clés d'indexes des attributs de configuration.
     *
     * @return string[]
     */
    public function keys()
    {
        return array_keys($this->attributes);
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
     * Récupération une liste d'attributs de configuration.
     *
     * @param string[] $keys Clé d'index des attributs de configuration à retourner.
     * @param array $customs Liste des attributs personnalisés.
     *
     * @return array
     */
    public function compact($keys = [], $customs = [])
    {
        if (empty($keys)) :
            return $this->all();
        endif;

        $attrs = [];
        foreach ($keys as $key) :
            $attrs[$key] = $this->get($key);
        endforeach;

        return array_merge(
            $attrs,
            $customs
        );
    }

    /**
     * Récupération de l'identifiant de qualification du controleur.
     *
     * @return string
     */
    final public function getId()
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
     * Affichage.
     *
     * @return string
     */
    abstract protected function display();

    /**
     * Récupération de l'affichage du controleur depuis l'instance.
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->display();
    }
}