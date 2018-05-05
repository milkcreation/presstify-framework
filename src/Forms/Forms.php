<?php

namespace tiFy\Forms;

use tiFy\Apps\AppController;
use tiFy\Forms\Addons\Addons;
use tiFy\Forms\Buttons\Buttons;
use tiFy\Forms\Fields\Fields;
use tiFy\Forms\Factory;

final class Forms extends AppController
{
    /**
     * Formulaire courant (en cours de traitement)
     * @var
     */
    protected $current = null;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Déclaration des dépendances
        $this->appServiceShare(Addons::class, new Addons());
        $this->appServiceShare(Buttons::class, new Buttons());
        $this->appServiceShare(Fields::class, new Fields());
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        /// Déclaration des boutons personnalisés
        

        // Types de champs
        /// Instanciation
        FieldTypes::init();
        /// Déclaration des types de champs personnalisés
        do_action('tify_form_register_type');


        $this->appAddAction('init', null, 1);
        $this->appAddAction('wp', null, 0);
    }

    /**
     * Initialisation globale
     *
     * @return void
     */
    final public function init()
    {
        if (is_admin()) :
            $this->registration();
        endif;
    }

    /** == Déclaration des formulaires pour les requêtes ajax - !!! Modifier pour AddonAjaxSubmit puis rétablie pour AddonRecord
     * final public function admin_init(){ if( defined( 'DOING_AJAX' ) ) $this->registration();} == **/

    /** == Chargement de Wordpress complet == **/
    final public function wp()
    {
        if (!is_admin()) {
            $this->registration();
        }

        do_action('tify_form_loaded');
    }

    /**
     * CONTROLEURS
     */
    /** == Déclaration des formulaires == **/
    private function registration()
    {
        // Déclaration des formulaires
        /// Depuis la configuration statique
        foreach ((array)self::tFyAppConfig() as $id => $attrs) :
            $this->register($id, $attrs);
        endforeach;

        /// Depuis la déclaration dynamique    
        do_action('tify_form_register');
    }

    /** == Déclaration d'un formulaire == **/
    final public static function register($id, $attrs = [])
    {
        // retro-compatibilité
        if (!empty($attrs['ID'])) :
            $id = $attrs['ID'];
        else :
            $attrs['ID'] = $id;
        endif;

        $override_path = [];
        foreach ((array)self::getOverrideNamespaceList() as $namespace) :
            $override_path[] = $namespace . "\\Core\\Forms\\" . self::sanitizeControllerName($id);
        endforeach;

        $FactoryClassName = self::getOverride('\tiFy\Forms\Factory', $override_path);
        $form = self::$Factories[$id] = new $FactoryClassName($id, $attrs);

        return $form->getForm()->getID();
    }

    /* = CONTROLEURS = */
    /** == Récupération d'un formulaire déclaré == **/
    public static function has($id)
    {
        return isset(self::$Factories[$id]);
    }

    /** == Récupération d'un formulaire déclaré == **/
    public static function get($id)
    {
        if (self::has($id)) {
            return self::$Factories[$id];
        }
    }

    /** == Récupération de la liste des formulaires == **/
    public static function getList()
    {
        return self::$Factories;
    }

    /** == Définition du formulaire courant == **/
    public static function setCurrent($form = null)
    {
        if (!is_object($form)) {
            $form = self::get($form);
        }

        if (!$form instanceof \tiFy\Forms\Factory) {
            return;
        }

        self::$Current = $form;
        self::$Current->getForm()->onSetCurrent();

        return self::$Current;
    }

    /** == Définition du formulaire courant == **/
    public static function getCurrent()
    {
        return self::$Current;
    }

    /** == Définition du formulaire courant == **/
    public static function resetCurrent()
    {
        if (self::$Current) {
            self::$Current->getForm()->onResetCurrent();
        }

        self::$Current = null;
    }

    /** == Affichage du formulaire == **/
    public static function display($form_id = null, $echo = false)
    {
        // Bypass
        if (!$form = self::setCurrent($form_id)) {
            return;
        }

        // Traitement des options du formulaire    
        $output = "";
        $output .= "\n<div id=\"tiFyForm-{$form_id}\" class=\"tiFyForm\">";
        $output .= $form->display(false);
        $output .= "\n</div>";

        if ($echo) {
            echo $output;
        }

        return $output;
    }
}