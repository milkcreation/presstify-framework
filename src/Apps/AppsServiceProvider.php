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
     * @param string $classname Nom de la classe de l'application.
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
     * @param string $classname Nom de la classe de l'application.
     * @param string $key Clé d'index de l'attribut de configuration. Syntaxe à point permise. Tous les attributs si null.
     * @param mixed $default Valeur de l'attribut de configuration.
     *
     * @return void
     */
    public function getAttr($classname, $key = null, $default = null)
    {
        return Arr::get($this->apps, $key ? "{$classname}.{$key}" : "{$classname}");
    }

    /**
     * Définition d'un attribut d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     * @param string $key Clé d'index de l'attribut de configuration.
     * @param mixed $value Valeur de l'attribut de configuration.
     *
     * @return void
     */
    public function setAttr($classname, $key, $value)
    {
        Arr::set($this->apps, "{$classname}.{$key}", $value);
    }

    /**
     * Vérification de déclaration d'une application.
     *
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return bool
     */
    public function exists($classname)
    {
        return in_array($classname, $this->apps);
    }

    /**
     * Récupération de l'url vers un asset.
     * @todo
     *
     * @param string $filename Chemin relatif vers le fichier du dossier des assets.
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return string
     */
    public function getAsset($filename, $classname)
    {
        return $this->tfy->absUrl() . ltrim('/Components/Assets/src/', '/') . ltrim($filename, '/');
    }

    /**
     * Récupération du nom complet d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return string
     */
    public function getClassname($classname)
    {
        if (!$_classname = Arr::get($this->apps, "{$classname}.classname", '')) :
            $_classname = $this->getReflectionClass($classname)->getName();
            $this->setAttr($classname, 'classname', $_classname);
        endif;

        return $_classname;
    }

    /**
     * Récupération d'un attribut ou de la liste des attributs de configuration d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     * @param string $key Clé d'index de l'attribut de configuration.
     * @param mixed $default Valeur de l'attribut de configuration.
     *
     * @return string
     */
    public function getConfig($classname, $key = null, $default = null)
    {
        $config = Arr::get($this->apps, "{$classname}.config", []);

        return !$key ? $config : Arr::get($config, $key, $default);
    }

    /**
     * Récupération du chemin absolu vers le repertoire de stockage d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return string
     */
    public function getDirname($classname)
    {
        if (!$dirname = Arr::get($this->apps, "{$classname}.dirname", '')) :
            $dirname = dirname($this->getFilename($classname));
            $this->setAttr($classname, 'filename', $dirname);
        endif;

        return $dirname;
    }

    /**
     * Récupération du chemin absolu vers le fichier d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return string
     */
    public function getFilename($classname)
    {
        if (!$filename = Arr::get($this->apps, "{$classname}.filename", '')) :
            $filename = $this->getReflectionClass($classname)->getFileName();
            $this->setAttr($classname, 'filename', $filename);
        endif;

        return $filename;
    }

    /**
     * Récupération de l'espace de nom d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return string
     */
    public function getNamespace($classname)
    {
        if (!$namespace = Arr::get($this->apps, "{$classname}.namespace", '')) :
            $namespace = $this->getReflectionClass($classname)->getNamespaceName();
            $this->setAttr($classname, 'namespace', $namespace);
        endif;

        return $namespace;
    }

    /**
     * Récupération de la classe de reflection d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return ReflectionClass
     */
    public function getReflectionClass($classname)
    {
        if (!$reflectionClass = Arr::get($this->apps, "{$classname}.reflectionClass", null)) :
            try {
                $reflectionClass = new ReflectionClass($classname);
            } catch (ReflectionException $e) {
                wp_die($e->getMessage(), __('Classe indisponible', 'tify'), $e->getCode());
            }
            $this->setAttr($classname, 'reflectionClass', $reflectionClass);
        endif;

        return $reflectionClass;
    }

    /**
     * Récupération du chemin relatif vers le repertoire de stockage d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return string
     */
    public function getRelPath($classname)
    {
        if (!$relPath = Arr::get($this->apps, "{$classname}.relPath", '')) :
            $relPath = (new fileSystem())->makePathRelative($this->getDirname($classname), $this->tfy->absPath());
            $this->setAttr($classname, 'relPath', $relPath);
        endif;

        return $relPath;
    }

    /**
     * Récupération du nom court d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return string
     */
    public function getShortname($classname)
    {
        if (!$shortname = Arr::get($this->apps, "{$classname}.shortname", '')) :
            $shortname = $this->getReflectionClass($classname)->getShortName();
            $this->setAttr($classname, 'shortname', $shortname);
        endif;

        return $shortname;
    }

    /**
     * Récupération de l'url vers le repertoire de stockage d'une application déclarée.
     *
     * @param object|string $classname Nom ou instance de l'application.
     *
     * @return string
     */
    public function getUrl($classname)
    {
        if (!$url = Arr::get($this->apps, "{$classname}.url", '')) :
            $url = home_url($this->getRelPath($classname));
            $this->setAttr($classname, 'url', $url);
        endif;

        return rtrim($url, '/');
    }
}