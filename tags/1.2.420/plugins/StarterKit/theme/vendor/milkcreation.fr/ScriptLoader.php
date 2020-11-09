<?php
namespace Milkcreation\Set;

class ScriptLoader extends \tiFy\App\Factory
{
    /* = ARGUMENTS = */
    // Liste des Actions à déclencher
    protected $tFyAppActions                = array(
        'init',
        'theme_before_enqueue_scripts',
        'admin_enqueue_scripts',
    );

    // Fonctions de rappel des actions
    protected $tFyAppActionsMethods    = array(
        'init'                              => 'register_scripts',
        'theme_before_enqueue_scripts'      => 'enqueue_scripts'        
    );
    
    /* = DECLENCHEURS = */    
    /** == Déclaration des scripts == **/
    public function register_scripts()
    {
        $min = SCRIPT_DEBUG ? '' : '.min';
        $version = 170415;

        // STYLES                
        /// POLICE DE CARACTERE TIGREBLANC
        tify_register_style(
            'milkcreationFont',
            array(
                'src'        => self::tFyAppUrl() .'/font/styles.css',
                'version'    => $version
            )
        );
    }

    /** == Mise en file des scripts de l'interface utilisateur == **/
    public function enqueue_scripts()
    {
        wp_enqueue_style( 'milkcreationFont' );
    }    

    /** == Mise en file des scripts de l'interface administrateur == **/
    final public function admin_enqueue_scripts()
    {
        wp_enqueue_style( 'milkcreationFont' );
    }
}