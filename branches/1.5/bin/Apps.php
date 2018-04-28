<?php
namespace tiFy;

use Illuminate\Support\Arr;
use tiFy\tiFy;
use tiFy\Lib\File;
use Symfony\Component\Yaml\Yaml;
use tiFy\Core\Db\Db;
use tiFy\Core\Labels\Labels;
use tiFy\Core\Templates\Templates;

final class Apps
{
    /**
     * Classe de rappel du controleur principal de PresstiFy
     * @var tiFy
     */
    protected $tiFy;

    /**
     * Liste des applications déclarés.
     * @var array
     */
    protected $registered = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(tiFy $tiFy)
    {
        $this->tiFy = $tiFy;

        // Chargement des MustUse
        foreach(glob(TIFY_PLUGINS_DIR . '/*', GLOB_ONLYDIR) as $plugin_dir) :
            if(! file_exists("{$plugin_dir}/MustUse")) :
                continue;
            endif;
            if(! $dh = @ opendir("{$plugin_dir}/MustUse")) :
                continue;
            endif;
            while(($file = readdir( $dh )) !== false) :
                if(substr( $file, -4 ) == '.php') :
                    include_once("{$plugin_dir}/MustUse/{$file}");
                endif;
            endwhile;
        endforeach;

        add_action('after_setup_theme', [$this, 'after_setup_theme'], 0);
    }

    /**
     * Après l'initialisation du thème.
     *
     * @return void
     */
    final public function after_setup_theme()
    {
        // Récupération des fichiers de configuration natifs de PresstiFy
        $_dir = @ opendir($this->tiFy->absDir() . "/bin/config");
        if ($_dir) :
            while (($file = readdir($_dir)) !== false) :
                // Bypass
                if (substr($file, 0, 1) == '.') :
                    continue;
                endif;

                $basename = basename($file, ".yml");

                // Bypass
                if (!in_array($basename, ['config', 'core', 'plugins', 'set', 'schema'])) :
                    continue;
                endif;

                if (!isset(${$basename})) :
                    ${$basename} = [];
                endif;

                ${$basename} = $this->parseFilePath(
                    $this->tiFy->absDir() . "/bin/config/{$file}",
                    ${$basename}
                );
            endwhile;

            closedir($_dir);
        endif;

        // Récupération du fichier de configuration personnalisée globale de PresstiFy
        $_dir = @ opendir(TIFY_CONFIG_DIR);
        if ($_dir) :
            while (($file = readdir($_dir)) !== false) :
                // Bypass
                if (substr($file, 0, 1) == '.') :
                    continue;
                endif;

                $basename = basename($file, "." . TIFY_CONFIG_EXT);

                // Bypass
                if ($basename !== 'config') :
                    continue;
                endif;

                if (!isset($config)) :
                    $config = [];
                endif;

                $config = $this->parseFilePath(
                    TIFY_CONFIG_DIR . "/" . $file,
                    $config,
                    TIFY_CONFIG_EXT
                );
            endwhile;

            closedir($_dir);
        endif;

        // Définition de l'environnement de développement de l'application.
        if ($app = $config['app']) :
            // Espace de nom
            if (! isset($app['namespace'])) :
                $app['namespace'] = 'App';
            endif;
            $app['namespace'] = trim($app['namespace'], '\\');

            // Répertoire de stockage
            if (! isset($app['base_dir'])) :
                $app['base_dir'] = get_template_directory() . "/app";
            endif;
            $app['base_dir'] = $app['base_dir'];

            // Point d'entrée unique
            if (! isset($app['bootstrap'])) :
                $app['bootstrap'] = 'Autoload';
            endif;
            $app['bootstrap'] = $app['bootstrap'];

            $this->tiFy->classLoad($app['namespace'], $app['base_dir'], (! empty($app['bootstrap']) ? $app['bootstrap'] : false));

            // Chargement automatique
            foreach (['core', 'plugins', 'set', 'schema'] as $dir) :
                if (! file_exists("{$app['base_dir']}/{$dir}")) :
                    continue;
                endif;

                $this->tiFy->classLoad($app['namespace']. "\\" . ucfirst($dir), $app['base_dir']. '/' . $dir, 'Autoload');
            endforeach;

            $config['app'] = $app;
        endif;

        // Enregistrement de la configuration globale de PresstiFy
        foreach ($config as $key => $value) :
            $this->tiFy->setConfig($key, $value);
        endforeach;

        // Chargement des traductions
        do_action('tify_load_textdomain');

        // Récupération des fichiers de configuration personnalisés des applicatifs (core|plugins|set|schema)
        $_dir = @ opendir(TIFY_CONFIG_DIR);
        if ($_dir) :
            while (($file = readdir($_dir)) !== false) :
                // Bypass
                if (substr($file, 0, 1 ) == '.') :
                    continue;
                endif;

                $basename = basename($file, ".". TIFY_CONFIG_EXT);

                // Bypass
                if (! in_array($basename, array('core', 'plugins', 'set', 'schema'))) :
                    continue;
                endif;

                if (! isset(${$basename})) :
                    ${$basename} = array();
                endif;

                ${$basename} += $this->parseFilePath(
                    TIFY_CONFIG_DIR . "/" . $file,
                    ${$basename},
                    TIFY_CONFIG_EXT
                );
            endwhile;
            closedir( $_dir );
        endif;

        foreach (['core', 'plugins', 'set', 'schema'] as $app) :
            // Bypass
            if (! isset(${$app})) :
                continue;
            endif;

            $App = ucfirst($app);
            $this->config[$App] = ${$app};
        endforeach;

        // Chargement des applications
        // Jeux de fonctionnalités
        // Enregistrement des jeux de fonctionnalités déclarés dans la configuration
        if (isset($this->config['Set'])) :
            foreach ($this->config['Set'] as $id => $attrs) :
                $this->register($id, $attrs);
            endforeach;
        endif;

        // Extensions
        // Enregistrement des extensions déclarées dans la configuration*
        if (isset($this->config['Plugins'])) :
            foreach ($this->config['Plugins'] as $id => $attrs) :
                $this->register($id, $attrs);
            endforeach;
        endif;

        // Composants natifs
        // Enregistrement des composants système
        foreach (glob($this->tiFy->absDir() . '/core/*', GLOB_ONLYDIR) as $dirname) :
            $id = basename($dirname);
            $attrs = isset($this->config['Core'][$id]) ? $this->config['Core'][$id] : [];
            $this->register($id, $attrs);
        endforeach;

        // Chargement de la configuration dans l'environnement de surcharge
        if ($app = $this->tiFy->getConfig('app')) :
            foreach (['core', 'plugins', 'set'] as $type) :
                $Type = ucfirst($type);

                foreach (glob($app['base_dir'] .'/'. $type .'/*/Config.php') as $filename):
                    $Id = basename(dirname($filename));
                    $overrideClass = "{$app['namespace']}\\{$Type}\\{$Id}\\Config";
                    if (class_exists($overrideClass) && is_subclass_of($overrideClass, 'tiFy\\App\\Config')) :
                        $attrs = call_user_func("tiFy\\{$Type}::register", $Id);
                        self::register($overrideClass, null, ['Parent' => $attrs['ClassName']]);
                        call_user_func([$overrideClass, '_ini_set']);
                    endif;
                endforeach;
            endforeach;
        endif;

        // Initialisation des schemas
        reset($this->registered);
        foreach ($this->registered as $classname => $attrs) :
            if(!in_array($attrs['Type'],['Core', 'Plugins', 'Set'])) :
                continue;
            endif;

            $dirname = $attrs['Dirname'] . '/config/';
            $schema = array();

            // Récupération du paramétrage natif
            $_dir = @ opendir($dirname);
            if ($_dir) :
                while (($file = readdir($_dir)) !== false) :
                    if (substr($file, 0, 1) == '.') :
                        continue;
                    endif;
                    $basename = basename($file, ".yml");
                    if ($basename !== 'schema') :
                        continue;
                    endif;

                    $schema += self::parseConfigFile("{$dirname}/{$file}", array(), 'yml', true);
                endwhile;
                closedir($_dir);
            endif;

            // Traitement du parametrage
            foreach ((array)$schema as $id => $entity) :
                /// Classe de rappel des données en base
                if (isset($entity['Db'])) :
                    Db::Register($id, $entity['Db']);
                endif;

                /// Classe de rappel des intitulés
                Labels::Register($id, (isset($entity['Labels']) ? $entity['Labels'] : array()));

                /// Gabarits de l'interface d'administration
                if (isset($entity['Admin'])) :
                    foreach ((array)$entity['Admin'] as $i => $tpl) :
                        if (!isset($tpl['db'])) :
                            $tpl['db'] = $id;
                        endif;
                        if (!isset($tpl['labels'])) :
                            $tpl['labels'] = $id;
                        endif;

                        Templates::Register($i, $tpl, 'admin');
                    endforeach;
                endif;

                /// Gabarits de l'interface utilisateur
                if (isset($entity['Front'])) :
                    foreach ((array)$entity['Front'] as $i => $tpl) :
                        if (!isset($tpl['db'])) :
                            $tpl['db'] = $id;
                        endif;
                        if (!isset($tpl['labels'])) :
                            $tpl['labels'] = $id;
                        endif;

                        Templates::Register($i, $tpl, 'front');
                    endforeach;
                endif;
            endforeach;
        endforeach;

        // Instanciation des applications déclarées
        foreach (array('set', 'plugins', 'core') as $type) :
            do_action("tify_{$type}_register");

            $Type = ucfirst($type);
            if (!$apps = self::query(['Type'=> $Type])) :
                continue;
            endif;

            foreach($apps as $classname => $attrs) :
                $this->tiFy->getContainer()->share($attrs['ClassName'], new $attrs['ClassName']);
            endforeach;
        endforeach;

        // Déclenchement des actions post-paramétrage
        do_action('after_setup_tify');
    }

    /**
     * Déclaration d'un application
     *
     * @param $classname
     *
     *
     * @return array
     */
    public function register($classname, $type = null, $attrs = array())
    {
        if (is_object($classname)) :
            $classname = get_class($classname);
        endif;

        // Bypass
        if (! class_exists($classname)):
            return;
        endif;

        $config_attrs = isset($attrs['Config']) ? $attrs['Config'] : [];

        if (! isset(self::$Registered[$classname])) :
            $ReflectionClass = new \ReflectionClass($classname);
            $ClassName = $ReflectionClass->getName();
            $ShortName = $ReflectionClass->getShortName();
            $LowerName = join('_', array_map('lcfirst', preg_split('#(?=[A-Z])#', lcfirst($ShortName))));
            $Namespace = $ReflectionClass->getNamespaceName();
            
            // Chemins
            $Filename = $ReflectionClass->getFileName();
            $Dirname = dirname($Filename);
            $Url = untrailingslashit(File::getFilenameUrl($Dirname, $this->tiFy->absPath()));
            $Rel = untrailingslashit(File::getRelativeFilename($Dirname, $this->tiFy->absPath()));
            
            // Traitement de la définition
            if (in_array($type, ['core', 'plugins', 'set'])) :
                $Id = isset($attrs['Id']) ? $attrs['Id'] : basename($classname);
                $Type = ucfirst($type);
            else :
                $Id = $ClassName;
                $Type = 'Customs';
            endif;

            // Configuration par défaut
            if (in_array($Type, ['Core', 'Plugins', 'Set'])) :
                $default_filename = $Dirname . '/config/config.yml';
                $ConfigDefault = file_exists($default_filename) ? $this->parseFileContent($default_filename) : [];
            else :
                $ConfigDefault = [];
            endif;

            // Configuration
            $Config = array_merge($ConfigDefault, $config_attrs);

            // Espace de nom de surchage
            $OverrideNamespace = '';

            // Chemins de surcharge
            $OverridePath = [];

            //Nom complet de la classe parente
            $Parent = (isset($attrs['Parent'])) ? $attrs['Parent'] : null;

            //Espace de nom enfant
            $ChildNamespace = null;
        else :
            /**
             * @var mixed $Config Attributs de configuration actifs
             */
            extract(self::$Registered[$classname]);
            $Config = wp_parse_args($config_attrs, $Config);
        endif;
        
        // Traitement de la configuration
        return self::$Registered[$classname] = compact(
            // Définition
            'Id',
            'Type',
            // Informations sur la classe
            'ReflectionClass',
            'ClassName',
            'ShortName',
            'LowerName',
            'Namespace',
            // Chemins d'accès
            'Filename',
            'Dirname',
            'Url',
            'Rel',
            // Attributs de configuration par défaut
            'ConfigDefault',
            // Attributs de configuration actifs
            'Config',
            // Espace de nom de surchage
            'OverrideNamespace',
            // Chemins de surcharge
            'OverridePath',
            // Nom complet de la classe parente
            'Parent',
            // Espace de nom enfant
            'ChildNamespace',
            // Instance de la classe de l'application
            'Instance'
        );
    }
    
    /**
     * Vérification d'existance d'une application selon son type ou non
     * 
     * @param object|string $classname Objet ou Nom de la classe de l'application
     * @param null|string $type Attribut de test vérification du type d'application. Core|Plugins|Set|Customs
     * 
     * @return bool
     */
    public function is($classname, $type = null)
    {
        if (is_object($classname)) :
            $classname = get_class($classname);
        endif;

        // Test parmis la liste complète des applications
        if (!$type) :
            return isset(self::$Registered[$classname]);
        endif;

        // Vérifie si le type de l'application est un type permis
        if(!in_array($type, ['Core', 'Plugins', 'Set', 'Customs'])) :
            return false;
        endif;

        // Vérifie d'abord si l'applications fait partie de la liste globale des applications déclarées
        if(!self::is($classname)) :
            return false;
        endif;

        $QueryClass = "self::query{$type}";
        $QueryApps = call_user_func($QueryClass);

        return isset($QueryApps[$classname]);
    }

    /**
     * Vérifie si une application fait partie des composants natifs déclarés
     *
     * @param object|string $classname Objet ou Nom de la classe de l'application
     *
     * @return bool
     */
    public function isAppCore($classname)
    {
        return $this->is($classname, 'Core');
    }

    /**
     * Vérifie si une application fait partie des plugins déclarés
     *
     * @param object|string $classname Objet ou Nom de la classe de l'application
     *
     * @return bool
     */
    public function isAppPlugin($classname)
    {
        return $this->is($classname, 'Plugins');
    }

    /**
     * Vérifie si une application fait partie des jeux de fonctionnalités déclarés
     *
     * @param object|string $classname Objet ou Nom de la classe de l'application
     *
     * @return bool
     */
    public function isAppSet($classname)
    {
        return $this->is($classname, 'Set');
    }

    /**
     * Vérifie si une application fait partie des applications personnalisées déclarées
     *
     * @param object|string $classname Objet ou Nom de la classe de l'application
     *
     * @return bool
     */
    public function isAppCustom($classname)
    {
        return $this->is($classname, 'Customs');
    }

    /**
     * Récupération d'une liste applications déclarés selon une liste de critères relatif aux attributs
     * 
     * @param array {
     *      @var string $Type Type d'application Core|Plugins|Set|Customs
     * }
     *
     * @return array
     */
    public function query($args = [])
    {
        $results = [];
        foreach ($this->registered as $classname => $attrs) :
            foreach ($args as $attr => $value):
                if (! isset($attrs[$attr]) || ($attrs[$attr] !== $value)) :
                    continue 2;
                endif;
            endforeach;
            $results[$classname] = $attrs;
        endforeach;
        
        return $results;
    }
    
    /**
     * Récupération la liste des composants natifs
     *
     * @return array
     */
    public function queryCore()
    {
        return $this->query(['Type' => 'Core']);
    }

    /**
     * Récupération la liste des extensions déclarées
     */
    public function queryPlugins()
    {
        return $this->query(['Type' => 'Plugins']);
    }

    /**
     * Récupération la liste des jeux de fonctionnalités déclarés
     */
    public function querySet()
    {
        return $this->query(['Type' => 'Set']);
    }

    /**
     * Récupération la liste des applications personnalisées
     */
    public function queryCustoms()
    {
        return $this->query(['Type' => 'Customs']);
    }

    /**
     * Récupération de la liste des attributs de configuration d'une application déclarée.
     *
     * @param object|string $classname Instance de l'application ou nom de la classe.
     *
     * @return array
     */
    public function getAttrList($classname)
    {
        if (is_object($classname)) :
            $classname = get_class($classname);
        endif;
                
        if (isset($this->registered[$classname])) :
            return $this->registered[$classname];
        endif;

        return [];
    }

    /**
     * Récupération d'un attribut d'application déclarée.
     *
     * @param string $key Clé d'indice de l'attribut de configuration.
     * @param string $default Valeur de retour par défaut.
     * @param object|string $classname Instance ou nom de la classe de l'application.
     *
     * @return mixed
     */
    public function getAttr($key, $default = '', $classname)
    {
        if (! $attrs = $this->getAttrList($classname)) :
            return $default;
        endif;

        return Arr::get($attrs, $key, $default);
    }

    /**
     * Définition d'un attribut d'application déclarée.
     *
     * @param string $key Clé de qualification de l'attribut à définir. Syntaxe à point permise pour permettre l'enregistrement de sous niveau.
     * @param mixed $value Valeur de définition de l'attribut.
     * @param object|string $classname Instance ou nom de la classe de l'application.
     *
     * @return bool
     */
    public function setAttr($key, $value, $classname)
    {
        if (! $attrs = $this->getAttrList($classname)) :
            return false;
        endif;

        Arr::set($attrs, $key, $value);

        return true;
    }
    
    /**
     * Définition d'attributs pour une application déclarée
     * 
     * @param array $attrs Liste des attributs.
     * @param object|string $classname Instance ou nom de la classe de l'application.
     * 
     * @return bool
     */
    public function setAttrList($attrs = [], $classname)
    {
        if (is_object($classname)) :
            $classname = get_class($classname);
        endif;
        
        if (! isset($this->registered[$classname])) :
            return false;
        endif;

        $this->registered[$classname] = array_merge(
            $this->registered[$classname],
            $attrs
        );
        
        return true;
    }

    /**
     * Définition de la liste des attributs de configuration d'une application déclarée
     *
     * @param string $name Qualification de l'attribut de configuration
     * @param mixed $value Valeur de l'attribut de configuration
     * @param object|string $classname Instance ou nom de la classe de l'application.
     *
     * @return bool
     */
    public function setConfigAttrList($attrs, $classname)
    {
        if (is_object($classname)) :
            $classname = get_class($classname);
        endif;

        if (! isset($this->registered[$classname])) :
            return false;
        endif;

        Arr::set($this->registered, "{$classname}.Config", $attrs);

        return true;
    }
    
    /**
     * Définition d'un attributs de configuration d'une application déclarée
     * 
     * @param string $name Qualification de l'attribut de configuration
     * @param mixed $value Valeur de l'attribut de configuration
     * @param object|string $classname Instance ou nom de la classe de l'application.
     * 
     * @return bool
     */
    public function setConfigAttr($name, $value = '', $classname)
    {
        if (is_object($classname)) :
            $classname = get_class($classname);
        endif;

        if (! isset($this->registered[$classname])) :
            return false;
        endif;

        Arr::set($this->registered, "{$classname}.Config.{$key}", $value);
        
        return true;
    }
    
    /**
     * Traitement d'un chemin de configuration.
     * 
     * @param string $path Chemin vers un fichier ou dossier de configuration.
     * @param array $current Attributs de configuration existant pour le chemin courant.
     * @param string $ext Extension du fichier de configuration.
     * @param bool $eval Traitement de la configuration de fichier.
     * 
     * @return array
     */
    public function parseFilePath($path, $current,  $ext = 'yml')
    {
        if (!is_dir($path)) :
            if (substr($path, -4) == ".{$ext}") :
                return array_merge(
                    $current,
                    (array)$this->parseFileContent($path, $ext)
                );
            endif;
        elseif ($subdir = @ opendir($path)) :
            $res = [];
            while (($subpath = readdir($subdir)) !== false) :
                if (substr($subpath, 0, 1) == '.') :
                    continue;
                endif;

                $subbasename = basename($subpath, ".{$ext}");

                $current[$subbasename] = isset($current[$subbasename]) ? $current[$subbasename] : [];
                $res[$subbasename] = $this->parseFilePath("$path/{$subpath}", $current[$subbasename], $ext);
            endwhile;

            closedir($subdir);

            return $res;
        endif;
    }

    /**
     * Traitement du fichier de configuration.
     * 
     * @param string $filename Chemin absolu vers le fichier de configuration.
     * @param string $ext Extension du fichier de configuration.
     * 
     * @return mixed
     */
    public function parseFileContent($filename, $ext = 'yml')
    {
        $output = Yaml::parse(file_get_contents($filename));

        return $output;
    }
}