<?php

namespace tiFy\Core\MetaTag;

use tiFy\App\Traits\App as TraitsApp;

abstract class AbstractController
{
    use TraitsApp;

    /**
     * Liste des instances
     * @var static
     */
    private static $instance = [];

    /**
     * Indicateur d'instanciation
     * @var bool
     */
    private $instanciated = false;

    /**
     * Compteur d'instance d'affichage
     * @var int
     */
    private $index = 0;

    /**
     * Liste des attributs de configuration
     * @var array
     */
    protected $attrs = [];

    /**
     * Liste des identifiant de qualification des attributs de configuration permis
     * @var array
     */
    protected $allowedAttrs = [];

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __construct()
    {
        // Initialisation des événements
        if (!did_action('init') && !$this->instanciated) :
            self::_tFyAppRegister($this);
            $this->instanciated = true;
            $this->events();
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
     * Initialisation
     *
     * @return self
     */
    final public static function make()
    {
        return new static();
    }

    /**
     * Instanciation
     *
     * @param string $id Identifiant de qualification du controleur d'affichage
     * @param array Attributs de configuration
     *
     * @return $this
     */
    final public function __invoke($id = null, $attrs = [])
    {
        $lower_name = $this->appLowerName();
        $instance_prefix = "tify.layout.{$lower_name}";

        if (is_array($id)) :
            $attrs = $id;
            $id = null;
        endif;

        if (is_null($id) && isset($attrs['id'])) :
            $id = $attrs['id'];
        elseif (is_null($id)) :
            $id = uniqid();
        endif;

        if (!isset(self::$instance["{$instance_prefix}.{$id}"])) :
            $instance = $this;
            $this->set('index', ++$this->index);
            $this->set('id', $id);
            self::$instance["{$instance_prefix}.{$id}"] = $instance;
        else :
            $instance = self::$instance["{$instance_prefix}.{$id}"];
        endif;

        // Traitement des attributs de configuration
        if ($attrs = $this->parse($attrs)) :
            foreach ($attrs as $name => $value) :
                if (!in_array($name, ['id', 'index'])) :
                    $this->set($name, $value);
                endif;
            endforeach;
        endif;

        return $instance;
    }

    /**
     * Récupération de l'affichage du controleur
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->display();
    }

    /**
     * Initialisation des événements
     *
     * @return void
     */
    protected function events()
    {
        if (method_exists($this, 'init')) :
            $this->appAddAction('init');
        endif;
    }

    /**
     * Traitement des attributs de configuration
     *
     * @param array $attrs Liste des attributs de configuration
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        return $attrs;
    }

    /**
     * Vérifie si un attribut de configuration est permis
     *
     * @param string $name Identifiant de qualification de l'attribut de configuration
     *
     * @return bool
     */
    final public function valid($name)
    {
        if (empty($this->allowedAttrs)) :
            return true;
        endif;

        return in_array($name, $this->allowedAttrs);
    }

    /**
     * Définition d'un attribut de configuration
     *
     * @param string $name Identifiant de qualification de l'attribut de configuration
     * @param mixed $value Valeur de retour de l'attribut
     *
     * @return $this
     */
    final public function set($name, $value)
    {
        if ($this->valid($name)) :
            $this->attrs[$name] = $value;
        endif;

        return $this;
    }

    /**
     * Récupération de la liste des attributs de configuration
     *
     * @return array
     */
    final public function all()
    {
        return $this->attrs;
    }

    /**
     * Vérification d'existance d'un attribut de configuration
     *
     * @param string $name Identifiant de qualification de l'attribut
     *
     * @return bool
     */
    final public function is($name)
    {
        return isset($this->attrs[$name]);
    }

    /**
     * Récupération d'un attribut de configuration
     *
     * @param string $name Identifiant de qualification de l'attribut
     * @param mixed $default Valeur de retour par défaut
     *
     * @return mixed
     */
    final public function get($name, $default = '')
    {
        if (!$this->is($name)) :
            return $default;
        endif;

        return $this->attrs[$name];
    }

    /**
     * Récupération de la valeur du compteur d'instance
     *
     * @return int
     */
    final public function getIndex()
    {
        return $this->get('index');
    }

    /**
     * Récupération de l'identifiant de qualification de la classe
     *
     * @return string
     */
    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * Récupération de la liste des clés d'indexes des attributs de configuration
     *
     * @return string[]
     */
    final public function keys()
    {
        return array_keys($this->attrs);
    }

    /**
     * Récupération de la liste des valeurs des attributs de configuration
     *
     * @return mixed[]
     */
    final public function values()
    {
        return array_values($this->attrs);
    }

    /**
     * Récupération une liste d'attributs de configuration
     *
     * @param string[] $keys Clé d'index des attributs à retourner
     *
     * @return array
     */
    final public function compact($keys = [])
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
     * Affichage
     *
     * @return string
     */
    protected function display()
    {
        return '';
    }
}