<?php

namespace tiFy\Kernel\Templates;

use Illuminate\Support\Arr;
use League\Plates\Engine;
use tiFy\Apps\AppControllerInterface;
use tiFy\Kernel\Templates\TemplateBaseController;

class Templates extends Engine
{
    /**
     * Classe de rappel du controleur d'application associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Liste des attributs de configuration.
     * @var array {
     *      @var string $ext Extension des fichiers de template.
     *      @var string $basedir Chemin absolu vers le répertoire des templates.
     *      @var string $controller Controleur de template.
     *      @var string $args Liste des variables passées en argument au controleur de template.
     * }
     */
    protected $attributes = [
        'ext'           => 'php',
        'basedir'       => '',
        'controller'    => TemplateBaseController::class,
        'args'          => []
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur d'application associée.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($app, $attrs = [])
    {
        $this->app = $app;
        $this->attributes = array_merge($this->attributes, $attrs);

        $directory = $this->app->appDirname() . '/templates';

        parent::__construct(is_dir($directory) ? $directory : null, $this->get('ext'));

        $subdir = '';
        if (!$basedir = $this->get('basedir', '')) :
            $basedir = get_template_directory() . '/templates';

            if(preg_match('#^\/?vendor/presstify-plugins/(.*)#', $this->app->appRelPath(), $matches)) :
                $basedir .= "/presstify-plugins/{$matches[1]}";
            endif;
        endif;

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
     * Récupération de la classe de rappel du controleur d'application associée.
     *
     * @return AppControllerInterface
     */
    public function app()
    {
        return $this->app;
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
     */
    public function make($name)
    {
        $name = $this->getFolders()->exists($this->app->appClassname()) ? "{$this->app->appClassname()}::{$name}" : $name;

        $controller = $this->get('controller');

        return new $controller($this, $name, $this->get('args', []));
    }
}