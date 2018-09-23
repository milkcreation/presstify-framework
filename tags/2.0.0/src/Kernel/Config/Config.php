<?php

namespace tiFy\Kernel\Config;

use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;
use tiFy\Kernel\Composer\ClassLoader;
use tiFy\Kernel\Filesystem\Paths;
use tiFy\Kernel\Parameters\AbstractParametersBagIterator;
use tiFy\tiFy;

class Config extends AbstractParametersBagIterator
{
    /**
     * Classe de rappel du controleur des chemins.
     * @var Paths
     */
    protected $paths;

    /**
     * Liste des alias.
     * @var array
     */
    protected $aliases = [
        'cron'        => \tiFy\Cron\Cron::class,
        'db'          => \tiFy\Db\Db::class,
        'media'       => \tiFy\Media\Media::class,
        'meta-tag'    => \tiFy\MetaTag\MetaTag::class,
        'page-hook'   => \tiFy\PageHook\PageHook::class,
        'route'       => \tiFy\Route\Route::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        /** @var Paths $paths */
        $this->paths = tiFy::instance()->resolve(Paths::class);

        if (file_exists($this->paths->getConfigPath('autoload.php'))) :
            $autoloads = include $this->paths->getConfigPath('autoload.php');
            foreach ($autoloads as $type => $autoload) :
                foreach ($autoload as $namespace => $path) :
                    tiFy::instance()->resolve(ClassLoader::class)->load($namespace, $path, $type);
                endforeach;
            endforeach;
        endif;

        $finder = (new Finder())->files()->name('/\.php$/')->in(\paths()->getConfigPath());
        foreach ($finder as $file) :
            $key = basename($file->getFilename(), ".{$file->getExtension()}");
            if ($key === 'autoload') :
                continue;
            endif;

            $value = include($file->getRealPath());

            switch($key) :
                default :
                    $this->set($this->getAlias($key), $value);
                    break;
                case 'plugins' :
                    foreach((array)$value as $plugin => $attrs) :
                        $this->set($plugin, $attrs);
                    endforeach;
                    break;
            endswitch;
        endforeach;
    }

    /**
     * Définition d'un attribut.
     *
     * @param string|array $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param null|mixed $value Valeur de l'attribut.
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) :
            parent::set($key, $value);
        endforeach;
    }

    /**
     * Récupération de l'alias de qualification d'un attribut de configuration.
     *
     * @param string $key Nom de qualification original.
     *
     * @return string
     */
    public function getAlias($key)
    {
        return Arr::get($this->getAliases(), $key, $key);
    }

    /**
     * Récupération de la liste des alias.
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }
}