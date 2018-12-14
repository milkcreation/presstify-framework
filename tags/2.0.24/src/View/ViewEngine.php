<?php

namespace tiFy\View;

use Illuminate\Support\Arr;
use League\Plates\Engine as LeaguePlatesEngine;
use tiFy\Contracts\View\ViewEngine as ViewEngineContract;

class ViewEngine extends LeaguePlatesEngine implements ViewEngineContract
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
        'controller'    => ViewController::class
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
    public function getController($name)
    {
        $controller = $this->get('controller');

        return new $controller($this, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getOverrideDir($path = '')
    {
        if ($this->folders->exists('_override')) :
            return $this->folders->get('_override')->getPath() . ($path ? trim($path, '/') : '');
        else :
            return '';
        endif;
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
        $view = $this->getController($name);
        $view->data($args);

        return $view;
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
        switch($key) :
            case 'controller' :
                $this->setController($value);
                break;
            case 'directory' :
                $this->setDirectory($value);
                break;
            case 'ext' :
                $this->setFileExtension($value);
                break;
            case 'override_dir' :
                $this->setOverrideDir($value);
                break;
            default :
                Arr::set($this->attributes, $key, $value);
                break;
        endswitch;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setController($controller)
    {
        Arr::set($this->attributes, 'controller', $controller);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirectory($directory)
    {
        Arr::set($this->attributes, 'directory', $directory);

        return parent::setDirectory($directory);
    }

    /**
     * {@inheritdoc}
     */
    public function setFileExtension($fileExtension)
    {
        Arr::set($this->attributes, 'ext', $fileExtension);

        return parent::setFileExtension($fileExtension);
    }

    /**
     * {@inheritdoc}
     */
    public function setOverrideDir($override_dir)
    {
        Arr::set($this->attributes, 'override_dir', $override_dir);

        $this->addFolder('_override', $override_dir, true);

        return $this;
    }
}