<?php

namespace tiFy\App;

use Illuminate\Support\Str;
use tiFy\App\Templates\AppEngine;
use tiFy\tiFy;

trait AppTrait
{
    /**
     * Classe de rappel de gestion des templates
     * @var AppEngine
     */
    protected $templatesAppEngine;

    /**
     * {@inheritdoc}
     */
    public function appAbsPath()
    {
        return \paths()->getBasePath();
    }

    /**
     * {@inheritdoc}
     */
    public function appAbsDir()
    {
        return \paths()->getTiFyPath();
    }

    /**
     * {@inheritdoc}
     */
    public function appAddAction($tag, $method = '', $priority = 10, $accepted_args = 1)
    {
        return $this->appAddFilter($tag, $method, $priority, $accepted_args);
    }

    /**
     * {@inheritdoc}
     */
    public function appAddFilter($tag, $method = '', $priority = 10, $accepted_args = 1)
    {
        if (!$method) :
            $method = $tag;
        endif;

        if (is_string($method) && !preg_match('#::#', $method)) :
            if ((new \ReflectionMethod($this, $method))->isStatic()) :
                $classname = get_class($this);
            else :
                $classname = $this;
            endif;

            $method = [$classname, $method];
        endif;

        if (!is_callable($method)) :
            return false;
        endif;

        return \add_filter($tag, $method, $priority, $accepted_args);
    }

    /**
     * {@inheritdoc}
     */
    public function appAssetUrl($path = '')
    {
        return $this->appAssets()->url($path);
    }

    /**
     * {@inheritdoc}
     */
    public function appAssets()
    {
        return \assets();
    }

    /**
     * {@inheritdoc}
     */
    public function appClassname()
    {
        return $this->appClassInfo()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function appConfig($key = null, $default = [])
    {
        $alias = $this->appContainer()->getAlias(get_class($this));

        if (is_array($key)) :
            return \config()->set($alias, $key);
        elseif(!empty($key)) :
            return \config()->get($alias . '.' .$key, $default);
        else :
            return \config()->get($alias, $default);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function appContainer()
    {
        return container();
    }

    /**
     * {@inheritdoc}
     */
    public function appDirname()
    {
        return $this->appClassInfo()->getDirname();
    }

    /**
     * {@inheritdoc}
     */
    public function appEventListen($name, $listener, $priority = 0)
    {
        return $this->appEvents()->listen($name, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function appEventTrigger($event)
    {
        return $this->appEvents()->trigger($event);
    }

    /**
     * {@inheritdoc}
     */
    public function appEvents()
    {
        return \events();
    }

    /**
     * {@inheritdoc}
     */
    public static function appInstance($classname = null, $args = [])
    {
        return \app($classname ?: get_called_class(), $args);
    }

    /**
     * {@inheritdoc}
     */
    public function appLog()
    {
        return \logger();
    }

    /**
     * {@inheritdoc}
     */
    public function appLowerName($name = null)
    {
        return Str::kebab($name ? : $this->appShortname());
    }

    /**
     * {@inheritdoc}
     */
    public function appNamespace()
    {
        return $this->appClassInfo()->getNamespaceName();
    }

    /**
     * {@inheritdoc}
     */
    public function appClassInfo()
    {
        return \class_info($this);
    }

    /**
     * {@inheritdoc}
     */
    public function appRelPath()
    {
        return $this->appClassInfo()->getRelPath();
    }

    /**
     * {@inheritdoc}
     */
    public function appRequest($property = '')
    {
        return \request()->getProperty($property);
    }

    /**
     * {@inheritdoc}
     */
    public function appServiceAdd($alias, $concrete = null, $singleton = false)
    {
        return $this->appContainer()->add($alias, $concrete, $singleton);
    }

    /**
     * {@inheritdoc}
     */
    public function appServiceGet($alias, $args = [])
    {
        return $this->appContainer()->get($alias, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function appServiceHas($alias)
    {
        return $this->appContainer()->has($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function appServiceShare($alias, $concrete = null)
    {
        return $this->appContainer()->share($alias, $concrete);
    }

    /**
     * {@inheritdoc}
     */
    public function appShortname()
    {
        return $this->appClassInfo()->getShortName();
    }

    /**
     * {@inheritdoc}
     */
    public function appTemplates($options = [])
    {
        if (!$this->templatesAppEngine) :
            $this->templatesAppEngine = new AppEngine($options, $this);
        endif;

        return $this->templatesAppEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function appTemplateMacro($name, $function)
    {
        return $this->appTemplates()->registerFunction($name, $function);
    }

    /**
     * {@inheritdoc}
     */
    public function appTemplateMake($name, $args = [])
    {
        return $this->appTemplates()->make($name, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function appTemplateRender($name, $args = [])
    {
        return $this->appTemplateMake($name, $args)->render();
    }

    /**
     * {@inheritdoc}
     */
    public function appTemplateView($name, $args = [])
    {
        return $this->appTemplates()->view($name, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function appUpperName($name = null)
    {
        return Str::camel($name ? : $this->appShortname());
    }

    /**
     * {@inheritdoc}
     */
    public function appUrl()
    {
        return $this->appClassInfo()->getUrl();
    }
}