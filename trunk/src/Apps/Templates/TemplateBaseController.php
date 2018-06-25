<?php

namespace tiFy\Apps\Templates;

use Illuminate\Support\Arr;
use League\Plates\Template\Template as LeagueTemplate;
use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\Templates\Templates;
use tiFy\Kernel\Tools;

class TemplateBaseController extends LeagueTemplate
{
    /**
     * Classe de rappel de l'application
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Liste des variables passées en argument.
     * @var array
     */
    protected $args = [];

    /**
     * Instance of the template engine.
     * @var Templates
     */
    protected $engine;

    /**
     * CONSTRUCTEUR.
     *
     * @param Engine $engine
     * @param string $name
     * @param array $args Liste des variables passées en argument
     *
     * @return void
     */
    public function __construct(Templates $engine, $name, $args = [], AppControllerInterface $app)
    {
        $this->app = $app;
        $this->args = $args;

        parent::__construct($engine, $name);
    }

    /**
     * Récupération de la liste complète des attributs de configuration.
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * Récupération de l'une variable passée en argument.
     *
     * @return string
     */
    public function getArg($key, $default = null)
    {
        return Arr::get($this->args, $key, $default);
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
        return Arr::has($this->data, $key);
    }

    /**
     * Linéarisation d'une liste d'attributs HTML.
     *
     * @return string
     */
    public function htmlAttrs($attrs)
    {
        return Tools::Html()->parseAttrs($attrs, true);
    }

    /**
     * Affichage d'un template frère.
     *
     * @return null
     */
    public function partial($name, $datas = [])
    {
        echo $this->insert($name, $datas);
    }
}