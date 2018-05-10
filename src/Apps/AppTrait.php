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
use tiFy\tiFy;

trait AppTrait
{
    /**
     * Récupération statique d'une instance de classe d'une application.
     *
     * @return static
     */
    public static function appInstance($classname = null)
    {
        return tiFy::instance()->serviceGet($classname ? : get_called_class());
    }

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
    public function appRequest($property = '')
    {
        return tiFy::instance()->request($property);
    }

    /**
     * Vérification d'existance d'une variable de requête globale
     *
     * @param string $key Identifiant de qualification de l'argument de requête
     * @param string $type Type de requête à traiter POST|GET|COOKIE|FILES|SERVER ...
     *
     *
     * @throws LogicException
     * @throws ReflectionException
     * @return mixed
     */
    public function appRequestHas($key, $type = '')
    {
        return tiFy::instance()->requestCall('has', compact('key'), $type);
    }

    /**
     * Récupération d'une variable de requête globale
     *
     * @param string $key Identifiant de qualification de l'argument de requête
     * @param mixed $default Valeur de retour par défaut
     * @param string $type Type de requête à traiter POST|GET|COOKIE|FILES|SERVER ...
     *
     * @return mixed
     */
    public function appRequestGet($key, $default = '', $type = '')
    {
        return tiFy::instance()->requestCall('get', compact('key', 'default'), $type);
    }

    /**
     * Définition d'une variable de requête globale
     *
     * @param array $parameters Liste des paramètres. Tableau associatif
     * @param string $type Type de requête à traiter POST|GET|COOKIE|FILES|SERVER ...
     *
     * @return mixed
     */
    public function appRequestAdd($parameters = [], $type = '')
    {
        return tiFy::instance()->requestCall('add', compact('parameters'), $type);
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
    public function appServiceAdd($alias, $concrete = null, $share = false)
    {
        return tiFy::instance()->serviceAdd($alias, $concrete, $share);
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
    public function appServiceHas($alias)
    {
        return tiFy::instance()->serviceHas($alias);
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
    public function appServiceProvider($provider)
    {
        return tiFy::instance()->serviceProvider($provider);
    }

    /**
     * {@inheritdoc}
     */
    public function appLowerName($name = null, $separator = '-')
    {
        return tiFy::instance()->formatLowerName($name ? : get_called_class(), $separator);
    }

    /**
     * {@inheritdoc}
     */
    public function appUpperName($name = null, $underscore = true)
    {
        return tiFy::instance()->formatUpperName($name ? : get_called_class(), $underscore);
    }

    /**
     * {@inheritdoc}
     */
    public function appRegister($classname = null, $attrs = [])
    {
        return tiFy::instance()->apps()->setApp($classname ? : get_called_class(), $attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function appExists($classname = null)
    {
        return tiFy::instance()->apps()->exists($classname ? : get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function appGet($key = null, $default = null, $classname = null)
    {
        return tiFy::instance()->apps()->getAttr($classname ? : get_called_class(), $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function appSet($key, $value, $classname = null)
    {
        return tiFy::instance()->apps()->setAttr($classname ? : get_called_class(), $key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function appReflectionClass($classname = null)
    {
        return tiFy::instance()->apps()->getReflectionClass($classname ? : get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function appClassname($classname = null)
    {
        return tiFy::instance()->apps()->getClassname($classname ? : get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function appShortname($classname = null)
    {
        return tiFy::instance()->apps()->getShortname($classname ? : get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function appNamespace($classname = null)
    {
        return tiFy::instance()->apps()->getNamespace($classname ? : get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function appDirname($classname = null)
    {
        return tiFy::instance()->apps()->getDirname($classname ? : get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function appRelPath($classname = null)
    {
        return tiFy::instance()->apps()->getRelPath($classname ? : get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function appUrl($classname = null)
    {
        return tiFy::instance()->apps()->getUrl($classname ? : get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function appConfig($key = null, $default = null, $classname = null)
    {
        return tiFy::instance()->apps()->getConfig($classname ? : get_called_class(), $key, $default);
    }

    /**
     * Ajout d'un filtre Wordpress.
     *
     * @param string $tag Identification de l'accroche.
     * @param string $class_method Méthode de la classe à executer.
     * @param int $priority Priorité d'execution.
     * @param int $accepted_args Nombre d'argument permis.
     * @param string|object $classname Nom de la classe ou instance de l'application.
     *
     * @return bool
     */
    public function appAddFilter($tag, $method = '', $priority = 10, $accepted_args = 1, $classname = null)
    {
        if (! $method) :
            $method = $tag;
        endif;

        if (is_string($method) && ! preg_match('#::#', $method)) :
            if (! $classname) :
                if ((new \ReflectionMethod($this, $method))->isStatic()) :
                    $classname = get_called_class();
                else :
                    $classname = $this;
                endif;
            endif;

            $method = [$classname, $method];
        endif;

        if (!is_callable($method)) :
            return false;
        endif;

        return \add_filter($tag, $method, $priority, $accepted_args);
    }

    /**
     * Ajout d'une action Wordpress.
     *
     * @param string $tag Identification de l'accroche.
     * @param string $method Méthode de la classe à executer.
     * @param int $priority Priorité d'execution.
     * @param int $accepted_args Nombre d'argument permis.
     * @param string|object $classname Nom de la classe ou instance de l'application.
     *
     * @return bool
     */
    public function appAddAction($tag, $method = '', $priority = 10, $accepted_args = 1, $classname = null)
    {
        return $this->appAddFilter($tag, $method, $priority, $accepted_args, $classname);
    }

    /**
     * Récupération de la classe de rappel du controleur de templates.
     *
     * @param string|object $classname Nom de la classe ou instance de l'application.
     *
     * @return \League\Plates\Engine
     */
    public function appTemplates($classname = null)
    {
        if (! $templates = $this->appGet('templates', null, $classname)) :
            $templates = new \League\Plates\Engine(get_template_directory() . '/templates');

            $appTemplatePath = $this->appDirname($classname) . '/templates';
            if (is_dir($appTemplatePath)) :
                $templates->addFolder($classname ? : get_called_class(), $appTemplatePath, true);
            endif;

            $this->appSet('templates', $templates, $classname);
        endif;

        return $templates;
    }

    /**
     * @return string
     */
    public function appTemplateRender($name, $args = [], $classname = null)
    {
        $classname = $classname ? : get_called_class();

        $templates = $this->appTemplates($classname);
        $name = $templates->getFolders()->exists($classname) ? "{$classname}::{$name}" : $name;
        $template = $templates->make($name);

        return $template->render($args);
    }

    /**
     * Récupération du déclencheur d'événement
     *
     * @return Emitter
     */
    final public function appEmitter()
    {
        return tiFy::instance()->getEmitter();
    }

    /**
     * Déclaration d'un événement.
     * @see http://event.thephpleague.com/2.0/emitter/basic-usage/
     *
     * @param string $name Identifiant de qualification de l'événement.
     * @param callable|ListenerInterface $listener Fonction anonyme ou Classe de traitement de l'événement.
     * @param int $priority Priorité de traitement.
     *
     * @return EmitterInterface
     */
    final public function appListen($name, $listener, $priority = 0)
    {
        return $this->appEmitter()->addListener($name, $listener, $priority);
    }

    /**
     * Déclenchement d'un événement.
     * @see http://event.thephpleague.com/2.0/events/classes/
     *
     * @param string|object $event Identifiant de qualification de l'événement.
     * @param mixed $args Variable(s) passée(s) en argument.
     *
     * @return null|EventInterface
     */
    final public function appEmit($event, $args = null)
    {
        if (! is_object($event) && ! is_string($event)) :
            return null;
        endif;

        return $this->appEmitter()->emit(is_object($event) ? $event : Event::named($event), $args);
    }

    /**
     * Récupération de la classe de rappel de journalisation.
     *
     * @param object|string $classname Instance (objet) ou Nom de la classe de l'application
     *
     * @return Logger
     */
    public function appLog($classname = null)
    {
        if ($logger = $this->appGet('logger', null, $classname)) :
            return $logger;
        endif;

        $filename = WP_CONTENT_DIR . '/uploads/log.log';

        $formatter = new LineFormatter();
        $stream = new RotatingFileHandler($filename, 7);
        $stream->setFormatter($formatter);

        $logger = new Logger($this->appClassname($classname));
        $this->appSet('logger', $logger, $classname);

        if ($timezone = get_option('timezone_string')) :
            $logger->setTimezone(new \DateTimeZone($timezone));
        endif;
        $logger->pushHandler($stream);

        return $logger;
    }
}