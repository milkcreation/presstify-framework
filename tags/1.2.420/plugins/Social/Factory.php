<?php
namespace tiFy\Plugins\Social;

class Factory extends \tiFy\App\Factory
{    
    /**
     * Liste des attributs de configuration
     */
    private static $Attrs       = array();
    
    /**
     * Liste des options
     */
    private static $Options     = array();
 
    /**
     * Clé d'index de qualification des options
     */
    protected static $OptionID  = null;
    
    /**
     * CONSTRUCTEUR
     * 
     * @return void
     */
    public function __construct($attrs = array())
    {
        parent::__construct();

        // Définition des attributs de configuration
        self::$Attrs[self::tFyAppClassname()] = $attrs;
        
        // Définition des options
        self::$Options[static::$OptionID] = isset(Social::$Options[static::$OptionID]) ? Social::$Options[static::$OptionID] : array();
    }
    
    /**
     * CONTROLEURS
     */
    /**
     * Récupération d'attributs de configuration
     * 
     * @param null|string $name Intitulé de l'option à retourner. Si null, retourne la liste complète des options.
     * @param string $default Valeur de retour par défaut
     * 
     * @return mixed|string
     */
    public static function getAttr($name = null, $default = '')
    {
        $classname = self::tFyAppClassname();
        
        if(! $name) :
            return self::$Attrs[$classname];
        elseif(isset(self::$Attrs[$classname][$name])) :
            return self::$Attrs[$classname][$name];
        else :
            return $default;
        endif;
    }
    
    /**
     * Récupération d'options
     * 
     * @param null|string $name Intitulé de l'option à retourner. Si null, retourne la liste complète des options.
     * @param string $default Valeur de retour par défaut
     * 
     * @return mixed|string
     */
    public static function getOption($name = null, $default = '')
    {
        if(! $name) :
            return self::$Options[static::$OptionID];
        elseif(isset(self::$Options[static::$OptionID][$name])) :
            return self::$Options[static::$OptionID][$name];
        else :
            return $default;
        endif;
    }
}