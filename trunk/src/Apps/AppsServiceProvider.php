<?php

namespace tiFy\Apps;

use App\App;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\Exception\NotFoundException;
use League\Container\ServiceProvider\AbstractServiceProvider as LeagueAbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use LogicException;
use ReflectionClass;
use ReflectionException;
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

        // Déclaration de l'application
        if ($app = $this->tfy->getConfig('app', [])) :
            $classname = Arr::get($app, 'classname', App::class);

            $this->setApp($classname, $app);
            array_push($this->bootable, $classname);
        endif;

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

            if (!class_exists($classname)) :
                continue;
            endif;

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
     * Récupération du nom de qualification d'un application.
     *
     * @param string|AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return void
     */
    public function getAppName($app)
    {
        if (is_object($app)) :
            $appName = get_class($app);
        elseif (class_exists($app)) :
            $appName = $app;
        endif;

        return $appName;
    }

    /**
     * Récupération d'un attribut d'une application déclarée.
     *
     * @param string $key Clé d'index de l'attribut de configuration. Syntaxe à point permise. Tous les attributs si null.
     * @param mixed $default Valeur de l'attribut de configuration.
     * @param string|AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return void
     */
    public function getAttr($key = null, $default = null, $app)
    {
        $appName = $this->getAppName($app);

        return Arr::get($this->apps, $key ? "{$appName}.{$key}" : "{$appName}");
    }

    /**
     * Récupération d'un attribut ou de la liste des attributs de configuration d'une application déclarée.
     *
     * @param string $key Clé d'index de l'attribut de configuration.
     * @param mixed $default Valeur de l'attribut de configuration.
     * @param string|AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
     */
    public function getConfig($key = null, $default = null, $app)
    {
        $appName = $this->getAppName($app);

        $config = Arr::get($this->apps, "{$appName}.config", []);

        return !$key ? $config : Arr::get($config, $key, $default);
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
     * Vérification de déclaration d'une application.
     *
     * @param string|AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return bool
     */
    public function exists($app)
    {
        $appName = $this->getAppName($app);

        return in_array($appName, $this->apps);
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
     * @param string|AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return bool
     */
    public function setApp($app, $attrs = [])
    {
        $appName = $this->getAppName($app);

        if (!$exists = Arr::get($this->apps, $appName, [])) :
            Arr::set($this->apps, $appName, []);
            array_push($this->provides, $appName);
            if (preg_match("#^tiFy\\\Plugins\\\#", $appName)) :
                array_push($this->bootable, $appName);
            endif;
        endif;

        // Traitement des attributs la configuration
        $this->apps[$appName]['config'] = array_merge(
            Arr::get($this->apps, "{$appName}.config", []),
            $attrs
        );

        return true;
    }

    /**
     * Définition d'un attribut d'une application déclarée.
     *
     * @param string $key Clé d'index de l'attribut de configuration.
     * @param mixed $value Valeur de l'attribut de configuration.
     * @param string|AppControllerInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return void
     */
    public function setAttr($key, $value, $app)
    {
        $appName = $this->getAppName($app);

        Arr::set($this->apps, "{$appName}.{$key}", $value);
    }
}