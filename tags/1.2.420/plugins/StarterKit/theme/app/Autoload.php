<?php 
namespace App;

class Autoload
{
    /**
     * CONSTRUCTEUR
     */
    public function __construct()
    {
        new GeneralTemplate;
        new Query;
        new ScriptLoader;
        new Theme;

        include( 'Helpers.php' );
    }
}