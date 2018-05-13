<?php

/**
 * @name API
 * @package PresstiFy
 * @subpackage Components
 * @namespace tiFy\Api
 * @desc Gestion d'API
 * @author Jordy Manner
 * @copyright Tigre Blanc Digital
 * @version 1.2.369
 */

namespace tiFy\Api;

use tiFy\Apps\AppController;

final class Api extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        if ($apis = $this->appConfig()) :
            foreach ($apis as $api => $attrs) :
                $this->register($api, $attrs);
            endforeach;
        endif;
    }

    /**
     * Déclaration d'une api.
     *
     * @param string $name Nom de qualification de l'Api.
     * @param array $attrs Attributs de configuration.
     *
     * @return null|object
     */
    public function register($name, $attrs = [])
    {         
        $name = $this->appUpperName($name);
        $classname = "tiFy\\Api\\{$name}\\{$name}";

        if (class_exists($classname)) :
            $instance = $classname::create($attrs);
            $this->appServiceShare($classname, $instance);

            return $this->appServiceGet($classname);
        endif;
    }
    
    /**
     * Récupération de la classe de rappel d'une Api.
     *
     * @param string $name Nom de qualification de l'Api.
     *
     * @return null|object
     */
    public function get($name)
    {
        $name = $this->appUpperName($name);

        if ($this->appServiceHas("tiFy\\Api\\{$name}\\{$name}")) :
            return $this->appServiceGet("tiFy\\Api\\{$name}\\{$name}");
        endif;
    }
}