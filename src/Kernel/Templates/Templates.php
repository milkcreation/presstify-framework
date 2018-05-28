<?php

namespace tiFy\Kernel\Templates;

use Illuminate\Support\Arr;
use League\Plates\Engine;
use tiFy\Apps\AppControllerInterface;
use tiFy\Kernel\Templates\Template;

class Templates extends Engine
{
    /**
     * Classe de rappel du controleur d'application associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur d'application associée.
     * @param array $attrs Liste des attributs de configuration
     *
     * @return void
     */
    public function __construct($app, $attrs = [])
    {
        $this->app = $app;
        $this->attributes = array_merge($this->attributes, $attrs);

        $directory = $this->app->appDirname() . '/templates';

        parent::__construct(is_dir($directory) ? $directory : null);

        $basedir = get_template_directory() . '/templates';

        $subdir = '';
        if(preg_match('#^\/?vendor/presstify-plugins/(.*)#', $this->app->appRelPath(), $matches)) :
            $subdir = "/presstify-plugins/{$matches[1]}";
        endif;

        if(is_dir($basedir . $subdir)) :
            $this->addFolder($this->app->appClassname(), $basedir . $subdir, true);
        else :
            $this->addFolder($this->app->appClassname(), $basedir, true);
        endif;

        $this->app->appSet('templates', $this);
    }

    /**
     * {@inheritdoc}
     */
    public function make($name)
    {
        $name = $this->getFolders()->exists($this->app->appClassname()) ? "{$this->app->appClassname()}::{$name}" : $name;

        return new Template($this, $name);
    }
}