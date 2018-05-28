<?php

namespace tiFy\Apps;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\Exception\NotFoundException;
use League\Container\ServiceProvider\AbstractServiceProvider as LeagueAbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use LogicException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Filesystem\Filesystem;
use tiFy\Apps\AppControllerInterface;
use tiFy\tiFy;

final class AppsServiceProvider extends LeagueAbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * Classe de rappel du controleur principal de PresstiFy.
     * @var tiFy
     */
    protected $tfy;

    /**
     * Liste des identifiants de qualification de services fournis.
     * @internal requis. Tous les alias de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [];

    /**
     * Listes des services déclarés, instanciés au démarrage.
     * @var array
     */
    protected $bootable = [];

    /**
     * Liste des attributs d'applications déclarées.
     * @var array
     */
    protected $apps = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(tiFy $tfy)
    {
        $this->tfy = $tfy;

        // Déclaration des applications configurées
        if ($apps = $this->tfy->getConfig('apps', [])) :
            foreach ($apps as $classname => $attrs) :
                $this->setApp($classname, $attrs);
                array_push($this->bootable, $classname);
            endforeach;
        endif;

        // Déclaration des extensions
        if ($plugins = $this->tfy->getConfig('plugins', [])) :
            foreach ($plugins as $classname => $attrs) :
                $this->setApp($classname, $attrs);
            endforeach;
        endif;

        // Déclaration des composants natifs
        foreach (glob($this->tfy->absDir() . '/*', GLOB_ONLYDIR) as $dirname) :
            $name = basename($dirname);
            $classname = "tiFy\\{$name}\\{$name}";
            $config = $tfy->getConfig(Str::kebab($name)) ? : [];

            $this->setApp($classname, $config);
            array_push($this->bootable, $classname);
        endforeach;
    }

    /**
     * Déclaration des services instanciés au démarrage.
     *
     * @return void
     */
    public function boot()
    {
        // Démarrage des applications déclarées dans la configuration
        foreach ($this->bootable as $key => $app) :
            if (in_array($app, array_keys($this->apps))) :
                $this->getContainer()->share($app, new $app());
                unset($this->bootable[$key]);
            endif;
        endforeach;

        do_action('tify_app_register', $this);

        // Démarrage des applications déclarées dans les controleurs
        foreach ($this->bootable as $key => $app) :
            if (in_array($app, array_keys($this->apps))) :
                $this->getContainer()->share($app, new $app());
                unset($this->bootable[$key]);
            endif;
        endforeach;

        // Déclenchement des actions post-paramétrage
        do_action('after_setup_tify');
    }

    /**
     * Déclaration des services instanciés de manière différées.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Récupération de la classe de rappel du conteneur d'injection utilisé par le fournisseur de service.
     *
     * @return ContainerInterface|Container
     */
    public function getContainer()
    {
        return parent::getContainer();
    }

    /**
     * Récupération ponctuelle d'une instance de service déclaré.
     * @internal La Syntaxe à points est autorisée pour les identifiants de qualification de sevice.
     * Elle permet une récupération en profondeur dans les clés d'un tableau dimensionné.
     *
     * @param string $alias Identifiant des services fournis.
     *
     * @return object
     *
     * @throws NotFoundException
     */
    public function resolve($alias, $args = [])
    {
        try {
            return $this->getContainer()->get($alias, $args);
        } catch (NotFoundException $e) {
            \wp_die($e->getMessage(), __('Récupération de l\'instance impossible', 'tify'), 500);
            exit;
        }
    }

    /**
     * Déclaration d'un application.
     *
     * @param string $classname Nom de la classe de l'application à déclarer.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return bool
     */
    public function setApp($classname, $attrs = [])
    {
        if (is_object($classname)) :
            $classname = get_class($classname);
        endif;

        // Bypass
        if (!class_exists($classname)) :
            return false;
        endif;

        if (!$exists = Arr::get($this->apps, $classname, [])) :
            Arr::set($this->apps, $classname, []);
            array_push($this->provides, $classname);
            if (preg_match("#^tiFy\\\Plugins\\\#", $classname)) :
                array_push($this->bootable, $classname);
            endif;
        endif;

        // Traitement des attributs la configuration
        $this->apps[$classname]['config'] = array_merge(
            Arr::get($this->apps, "{$classname}.config", []),
            $attrs
        );

        return true;
    }

    /**
     * Récupération d'un attribut d'une application déclarée.
     *
     * @param string $key Clé d'index de l'attribut de configuration. Syntaxe à point permise. Tous les attributs si null.
     * @param mixed $default Valeur de l'attribut de configuration.
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return void
     */
    public function getAttr($key = null, $default = null, $app)
    {
        $classname = get_class($app);

        return Arr::get($this->apps, $key ? "{$classname}.{$key}" : "{$classname}");
    }

    /**
     * Définition d'un attribut d'une application déclarée.
     *
     * @param string $key Clé d'index de l'attribut de configuration.
     * @param mixed $value Valeur de l'attribut de configuration.
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return void
     */
    public function setAttr($key, $value, $app)
    {
        $classname = get_class($app);

        Arr::set($this->apps, "{$classname}.{$key}", $value);
    }

    /**
     * Vérification de déclaration d'une application.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return bool
     */
    public function exists($app)
    {
        $classname = get_class($app);

        return in_array($classname, $this->apps);
    }

    /**
     * Récupération de l'url vers un asset.
     * @todo
     *
     * @param string $filename Chemin relatif vers le fichier du dossier des assets.
     *
     * @return string
     */
    public function getAsset($filename)
    {
        return $this->tfy->absUrl() . ltrim('/Components/Assets/src/', '/') . ltrim($filename, '/');
    }

    /**
     * Récupération du nom complet d'une application déclarée.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
     */
    public function getClassname($app)
    {
        $classname = get_class($app);

        if (!$_classname = Arr::get($this->apps, "{$classname}.classname", '')) :
            $_classname = $this->getReflectionClass($app)->getName();
            $this->setAttr('classname', $_classname, $app);
        endif;

        return $_classname;
    }

    /**
     * Récupération d'un attribut ou de la liste des attributs de configuration d'une application déclarée.
     *
     * @param string $key Clé d'index de l'attribut de configuration.
     * @param mixed $default Valeur de l'attribut de configuration.
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
     */
    public function getConfig($key = null, $default = null, $app)
    {
        $classname = get_class($app);

        $config = Arr::get($this->apps, "{$classname}.config", []);

        return !$key ? $config : Arr::get($config, $key, $default);
    }

    /**
     * Récupération du chemin absolu vers le repertoire de stockage d'une application déclarée.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
     */
    public function getDirname($app)
    {
        $classname = get_class($app);

        if (!$dirname = Arr::get($this->apps, "{$classname}.dirname", '')) :
            $dirname = dirname($this->getFilename($app));
            $this->setAttr('dirname', $dirname, $app);
        endif;

        return $dirname;
    }

    /**
     * Récupération du chemin absolu vers le fichier d'une application déclarée.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
     */
    public function getFilename($app)
    {
        $classname = get_class($app);

        if (!$filename = Arr::get($this->apps, "{$classname}.filename", '')) :
            $filename = $this->getReflectionClass($app)->getFileName();
            $this->setAttr('filename', $filename, $app);
        endif;

        return $filename;
    }

    /**
     * Récupération de l'espace de nom d'une application déclarée.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
     */
    public function getNamespace($app)
    {
        $classname = get_class($app);

        if (!$namespace = Arr::get($this->apps, "{$classname}.namespace", '')) :
            $namespace = $this->getReflectionClass($app)->getNamespaceName();
            $this->setAttr('namespace', $namespace, $app);
        endif;

        return $namespace;
    }

    /**
     * Récupération de la classe de reflection d'une application déclarée.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return ReflectionClass
     */
    public function getReflectionClass($app)
    {
        $classname = get_class($app);

        if (!$reflectionClass = Arr::get($this->apps, "{$classname}.reflectionClass", null)) :
            try {
                $reflectionClass = new ReflectionClass($classname);
            } catch (ReflectionException $e) {
                wp_die($e->getMessage(), __('Classe indisponible', 'tify'), $e->getCode());
            }
            $this->setAttr('reflectionClass', $reflectionClass, $app);
        endif;

        return $reflectionClass;
    }

    /**
     * Récupération du chemin relatif vers le repertoire de stockage d'une application déclarée.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
     */
    public function getRelPath($app)
    {
        $classname = get_class($app);

        if (!$relPath = Arr::get($this->apps, "{$classname}.relPath", '')) :
            $relPath = (new fileSystem())->makePathRelative($this->getDirname($app), $this->tfy->absPath());
            $this->setAttr('relPath', $relPath, $app);
        endif;

        return $relPath;
    }

    /**
     * Récupération du nom court d'une application déclarée.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
     */
    public function getShortname($app)
    {
        $classname = get_class($app);

        if (!$shortname = Arr::get($this->apps, "{$classname}.shortname", '')) :
            $shortname = $this->getReflectionClass($app)->getShortName();
            $this->setAttr('shortname', $shortname, $app);
        endif;

        return $shortname;
    }

    /**
     * Récupération de l'url vers le repertoire de stockage d'une application déclarée.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
     */
    public function getUrl($app)
    {
        $classname = get_class($app);

        if (!$url = Arr::get($this->apps, "{$classname}.url", '')) :
            $url = home_url($this->getRelPath($app));
            $this->setAttr('url', $url, $app);
        endif;

        return rtrim($url, '/');
    }
}