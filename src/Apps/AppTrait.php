<?php

namespace tiFy\Apps;

use League\Event\Emitter;
use League\Event\EmitterInterface;
use League\Event\Event;
use League\Event\EventInterface;
use League\Event\ListenerInterface;
use League\Plates\Engine;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use tiFy\Kernel\Templates\Templates;
use tiFy\tiFy;

trait AppTrait
{
    /**
     * {@inheritdoc}
     */
    public function appAbsPath()
    {
        return tiFy::instance()->absPath();
    }

    /**
     * {@inheritdoc}
     */
    public function appAbsDir()
    {
        return tiFy::instance()->absDir();
    }

    /**
     * {@inheritdoc}
     */
    public function appAbsUrl()
    {
        return tiFy::instance()->absUrl();
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
        if (! $method) :
            $method = $tag;
        endif;

        if (is_string($method) && ! preg_match('#::#', $method)) :
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
    public function appAsset($filename)
    {
        return tiFy::instance()->apps()->getAsset($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function appClassLoad($namespace, $base_dir = null)
    {
        return tiFy::instance()->classLoad($namespace, $base_dir);
    }

    /**
     * {@inheritdoc}
     */
    public function appClassname()
    {
        return tiFy::instance()->apps()->getClassname($this);
    }

    /**
     * {@inheritdoc}
     */
    public function appConfig($key = null, $default = null)
    {
        return tiFy::instance()->apps()->getConfig($key, $default, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function appDirname()
    {
        return tiFy::instance()->apps()->getDirname($this);
    }

    /**
     * {@inheritdoc}
     */
    public function appEvent()
    {
        return tiFy::instance()->emitter();
    }

    /**
     * {@inheritdoc}
     */
    public function appEventListen($name, $listener, $priority = 0)
    {
        return $this->appEvent()->addListener($name, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function appEventTrigger($event)
    {
        if (! is_object($event) && ! is_string($event)) :
            return null;
        endif;

        $args = func_get_args();
        $args[0] = is_object($event) ? $event : Event::named($event);

        return call_user_func_array([$this->appEvent(), 'emit'], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function appExists()
    {
        return tiFy::instance()->apps()->exists($this);
    }

    /**
     * {@inheritdoc}
     */
    public function appGet($key = null, $default = null)
    {
        return tiFy::instance()->apps()->getAttr($key, $default, $this);
    }

    /**
     * {@inheritdoc}
     *
     * @return self|object
     */
    public static function appInstance($classname = null, $args = [])
    {
        return tiFy::instance()->serviceGet($classname ? : get_called_class(), $args);
    }

    /**
     * {@inheritdoc}
     */
    public function appLog()
    {
        if ($logger = $this->appGet('logger')) :
            return $logger;
        endif;

        $filename = WP_CONTENT_DIR . '/uploads/log.log';

        $formatter = new LineFormatter();
        $stream = new RotatingFileHandler($filename, 7);
        $stream->setFormatter($formatter);

        $logger = new Logger($this->appClassname());
        $this->appSet('logger', $logger);

        if ($timezone = get_option('timezone_string')) :
            $logger->setTimezone(new \DateTimeZone($timezone));
        endif;
        $logger->pushHandler($stream);

        return $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function appLowerName($name = null, $separator = '-')
    {
        return tiFy::instance()->formatLowerName($name ? : get_class($this), $separator);
    }

    /**
     * {@inheritdoc}
     */
    public function appNamespace()
    {
        return tiFy::instance()->apps()->getNamespace($this);
    }

    /**
     * {@inheritdoc}
     */
    public function appReflectionClass()
    {
        return tiFy::instance()->apps()->getReflectionClass($this);
    }

    /**
     * {@inheritdoc}
     */
    public function appRegister($attrs = [])
    {
        return tiFy::instance()->apps()->setApp(get_class($this), $attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function appRelPath()
    {
        return tiFy::instance()->apps()->getRelPath($this);
    }

    /**
     * {@inheritdoc}
     */
    public function appRequest($property = '')
    {
        return tiFy::instance()->request($property);
    }

    /**
     * {@inheritdoc}
     */
    public function appRequestHas($key, $type = '')
    {
        return tiFy::instance()->requestCall('has', compact('key'), $type);
    }

    /**
     * {@inheritdoc}
     */
    public function appRequestGet($key, $default = '', $type = '')
    {
        return tiFy::instance()->requestCall('get', compact('key', 'default'), $type);
    }

    /**
     * {@inheritdoc}
     */
    public function appRequestAdd($parameters = [], $type = '')
    {
        return tiFy::instance()->requestCall('add', compact('parameters'), $type);
    }

    /**
     * {@inheritdoc}
     */
    public function appServiceAdd($alias, $concrete = null, $share = false)
    {
        return tiFy::instance()->serviceAdd($alias, $concrete, $share);
    }

    /**
     * {@inheritdoc}
     */
    public function appServiceGet($alias, $args = [])
    {
        return tiFy::instance()->serviceGet($alias, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function appServiceHas($alias)
    {
        return tiFy::instance()->serviceHas($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function appServiceProvider($provider)
    {
        return tiFy::instance()->serviceProvider($provider);
    }

    /**
     * {@inheritdoc}
     */
    public function appServiceShare($alias, $concrete = null)
    {
        return tiFy::instance()->serviceShare($alias, $concrete);
    }

    /**
     * {@inheritdoc}
     */
    public function appSet($key, $value)
    {
        return tiFy::instance()->apps()->setAttr($key, $value, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function appShortname()
    {
        return tiFy::instance()->apps()->getShortname($this);
    }

    /**
     * {@inheritdoc}
     */
    public function appTemplates()
    {
        if (!$templates = $this->appGet('templates')) :
            $templates = new Templates($this);
        endif;

        return $templates;
    }

    /**
     * {@inheritdoc}
     */
    public function appTemplateMake($name)
    {
        return $this->appTemplates()->make($name);
    }

    /**
     * {@inheritdoc}
     */
    public function appTemplateRender($name, $args = [], $classname = null)
    {
        return $this->appTemplateMake($name, $classname)->render($args);
    }

    /**
     * {@inheritdoc}
     */
    public function appUpperName($name = null, $underscore = true)
    {
        return tiFy::instance()->formatUpperName($name ? : get_class($this), $underscore);
    }

    /**
     * {@inheritdoc}
     */
    public function appUrl()
    {
        return tiFy::instance()->apps()->getUrl($this);
    }
}