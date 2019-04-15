<?php

namespace tiFy\Wordpress\Partial;

use tiFy\Contracts\Partial\PartialManager;

class Partial
{
    /**
     * Instance du gestionnaire des gabarits d'affichage.
     * @var PartialManager
     */
    protected $manager;

    /**
     * CONSTRUCTEUR
     *
     * @param PartialManager $manager Instance du gestionnaire des gabarits d'affichage.
     *
     * @return void
     */
    public function __construct(PartialManager $manager)
    {
        $this->manager = $manager;

        foreach ([
                     'accordion',
                     'breadcrumb',
                     'cookie-notice',
                     'dropdown',
                     'holder',
                     'modal',
                     'navtabs',
                     'notice',
                     'pagination',
                     'sidebar',
                     'slider',
                     'spinner',
                     'table',
                     'tag',
                 ] as $alias) {
            $this->manager->register($alias, app()->get("partial.factory.{$alias}"));
        }
    }
}