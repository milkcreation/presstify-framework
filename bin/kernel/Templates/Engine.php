<?php

namespace tiFy\Kernel\Templates;

use Illuminate\Support\Arr;
use League\Plates\Engine as LeaguePlatesEngine;
use tiFy\Kernel\Kernel;
use tiFy\Kernel\TemplateController;
use tiFy\Kernel\TemplateInterface;

class Engine extends LeaguePlatesEngine
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *      @var string $directory Chemin absolu vers le répertoire par défaut des templates.
     *      @var string $ext Extension des fichiers de template.
     *      @var string $controller Controleur de template.
     * }
     */
    protected $attributes = [
        'directory'     => null,
        'ext'           => 'php',
        'controller'    => TemplateController::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration
     *
     * @return void
     */
    public function __construct($attrs = [])
    {
        $this->parse($attrs);

        $directory = $this->get('directory');

        parent::__construct(is_dir($directory) ? $directory : null, $this->get('ext'));
    }

    /**
     * Récupération de la liste complète des attributs de configuration.
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
    public function get($key, $default = '')
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Récupération du controleur de template.
     *
     * @return TemplateInterface
     */
    public function getController()
    {
        return $this->get('controller');
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
     * {@inheritdoc}
     *
     * @return TemplateInterface
     */
    public function make($name, $args = [])
    {
        $controller = $this->getController();

        return new $controller($this, $name);
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function parse($attrs = [])
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
}