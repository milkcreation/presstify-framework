<?php

namespace tiFy\App\Templates;

use Illuminate\Support\Arr;
use League\Plates\Engine as PlatesEngine;
use tiFy\App\AppInterface;

class Engine extends PlatesEngine
{
    /**
     * Classe de rappel du controleur d'application associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * Liste des attributs de configuration.
     * @var array {
     *      @var string $directory Chemin absolu vers le répertoire par défaut des templates.
     *      @var string $ext Extension des fichiers de template.
     *      @var string $basedir Chemin absolu vers le répertoire désigné des templates (surchage du répertoire par défaut).
     *      @var string $controller Controleur de template.
     *      @var string $args Liste des variables passées en argument au controleur de template.
     * }
     */
    protected $attributes = [
        'directory'     => null,
        'ext'           => 'php',
        'basedir'       => '',
        'controller'    => TemplateBaseController::class,
        'args'          => []
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration.
     * @param AppInterface $app Classe de rappel du controleur d'application associée.
     *
     * @return void
     */
    public function __construct($attrs = [], AppInterface $app)
    {
        $this->app = $app;

        $this->parse($attrs);

        $directory = $this->get('directory');
        parent::__construct(is_dir($directory) ? $directory : null, $this->get('ext'));

        $basedir = $this->get('basedir');
        if(is_dir($basedir)) :
            $this->addFolder($this->app->appClassname(), $basedir, true);
        endif;
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
     * @return TemplateControllerInterface
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
     * @return TemplateControllerInterface
     */
    public function make($name, $args = [])
    {
        $name = $this->getFolders()->exists($this->app->appClassname()) ? "{$this->app->appClassname()}::{$name}" : $name;

        $controller = $this->get('controller');

        /** @var TemplateControllerInterface $template */
        $template = new $controller($this, $name, $this->get('args', []), $this->app);
        $template->data($args);

        return $template;
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

    /**
     * Affichage d'une vue basé sur un template.
     *
     * @return TemplateControllerInterface
     */
    public function view($name, $args = [])
    {
        $template = $this->make($name, $args);

        $this->app->appAddAction(
            'template_redirect',
            function () use ($template) {
                echo $template->render();
                exit;
            },
            0
        );

        return $template;
    }
}