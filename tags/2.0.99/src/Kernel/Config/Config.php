<?php

namespace tiFy\Kernel\Config;

use Symfony\Component\Finder\Finder;
use tiFy\Kernel\Composer\ClassLoader;
use tiFy\Kernel\Filesystem\Paths;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\tiFy;

class Config extends ParamsBag
{
    /**
     * Classe de rappel du controleur des chemins.
     * @var Paths
     */
    protected $paths;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->paths = tiFy::instance()->get(Paths::class);

        if (file_exists($this->paths->getConfigPath('autoload.php'))) :
            $autoloads = include $this->paths->getConfigPath('autoload.php');
            foreach ($autoloads as $type => $autoload) :
                foreach ($autoload as $namespace => $path) :
                    tiFy::instance()->get(ClassLoader::class)->load($namespace, $path, $type);
                endforeach;
            endforeach;
        endif;

        $attrs = [];
        if (is_dir(paths()->getConfigPath())) :
            $finder = (new Finder())->files()->name('/\.php$/')->in(paths()->getConfigPath());
            foreach ($finder as $file) :
                $key = basename($file->getFilename(), ".{$file->getExtension()}");
                if ($key === 'autoload') :
                    continue;
                endif;

                $attrs[$key] = include($file->getRealPath());
            endforeach;
        endif;

        parent::__construct($attrs);

        $this->set('site_url', env('SITE_URL'));
    }
}