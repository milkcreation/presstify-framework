<?php

namespace tiFy\Apps\Templates;

use Illuminate\Support\Arr;
use League\Plates\Engine;
use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\Templates\TemplateBaseController;

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
     * @param AppControllerInterface $app Classe de rappel du controleur d'application associée.
     *
     * @return void
     */
    public function __construct($attrs = [], AppControllerInterface $app)
    {
        $this->app = $app;

        $this->parse($attrs);

        $directory = $this->get('directory');
        parent::__construct(is_dir($directory) ? $directory : null, $this->get('ext'));

        $basedir = $this->get('basedir');
        if(is_dir($basedir)) :
            $this->addFolder($this->app->appClassname(), $basedir, true);
        elseif ($directory !== get_template_directory() . '/templates') :
            $this->addFolder($this->app->appClassname(), get_template_directory() . '/templates', true);
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

        return new $controller($this, $name, $this->get('args', []), $this->app);
    }

    /**
     * Traiteement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function parse($attrs = [])
    {
        $this->set('directory', $this->app->appDirname() . '/templates');

        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        if (!$basedir = $this->get('basedir', '')) :
            $basedir = get_template_directory() . '/templates';

            if(preg_match('#^\/?vendor/presstify-plugins/(.*)#', $this->app->appRelPath(), $matches)) :
                $basedir .= "/presstify-plugins/{$matches[1]}";
            endif;
        endif;
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