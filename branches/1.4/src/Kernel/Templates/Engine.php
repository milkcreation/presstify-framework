<?php

namespace tiFy\Kernel\Templates;

use Illuminate\Support\Arr;
use League\Plates\Engine as LeaguePlatesEngine;
use tiFy\Kernel\Kernel;

class Engine extends LeaguePlatesEngine implements EngineInterface
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
     * @param string|array $attrs Liste des attributs de configuration
     *
     * @return void
     */
    public function __construct($attrs = [])
    {
        if (is_string($attrs)) :
            $directory = $attrs;
            $attrs = compact('directory');
        endif;

        $this->parse($attrs);

        $directory = $this->get('directory');

        parent::__construct(is_dir($directory) ? $directory : null, $this->get('ext'));
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
    public function get($key, $default = '')
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return $this->get('controller');
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
    public function make($name, $args = [])
    {
        $controller = $this->getController();

        /** @var TemplateInterface $template */
        $template = new $controller($this, $name);
        $template->data($args);

        return $template;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyFolder($name, $directory, $fallback = null)
    {
        if ($folder = $this->getFolders()->get($name)) :
            if (is_null($folder)) :
                $fallback = $folder->getFallback();
            endif;
            $this
                ->removeFolder($name)
                ->addFolder($name, $directory, $fallback);
        endif;

        return $this;
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
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        switch($key) :
            case 'directory' :
                $this->setDirectory($value);
                break;
            case 'ext' :
                $this->setFileExtension($value);
                break;
        endswitch;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setController($controller)
    {
        $this->set('controller', $controller);

        return $this;
    }
}