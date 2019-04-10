<?php

namespace tiFy\Kernel\Composer;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use tiFy\Kernel\Filesystem\Paths;
use tiFy\tiFy;

class ClassLoader extends ComposerClassLoader
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
    }

    /**
     * Déclaration d'un jeu de répertoire PSR-0|PSR-4 pour un espace de nom ou auto-inclusion de fichier.
     *
     * @param string $prefix Espace de nom de qualification.
     * @param array|string $paths Chemin(s) vers le(s) repertoire(s) de l'espace de nom.
     * @param string $type psr-4|psr-0|files|classmap @todo.
     *
     * @return $this
     */
    public function load($prefix, $paths, $type = 'psr-4')
    {
        switch ($type) :
            default :
            case 'psr-4' :
                $this->addPsr4($prefix, $paths);
                break;
            case 'psr-0' :
                $this->add($prefix, $paths);
                break;
            case 'files' :
                if (is_string($paths)) :
                    $paths = (array)$paths;
                endif;
                foreach($paths as $path) :
                    include_once $this->paths->getBasePath($path);
                endforeach;
                break;
            case 'classmap' :
                /** @todo */
                break;
        endswitch;

        $this->register();

        return $this;
    }
}