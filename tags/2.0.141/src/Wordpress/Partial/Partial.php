<?php declare(strict_types=1);

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
                     'notice',
                     'pagination',
                     'pdf-preview',
                     'sidebar',
                     'slider',
                     'spinner',
                     'tab',
                     'table',
                     'tag',
                 ] as $alias) {
            $this->manager->register($alias, app()->get("partial.factory.{$alias}"));
        }
    }
}