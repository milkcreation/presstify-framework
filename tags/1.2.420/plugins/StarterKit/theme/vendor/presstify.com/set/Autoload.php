<?php 
namespace PresstiFy\Set;

class Autoload
{
    /* = CONSTRUCTEUR = */
    public function __construct()
    {
        new Animations\Animations;
        new Templates\Templates;
    }
}