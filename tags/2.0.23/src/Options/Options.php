<?php

/**
 * @name Options.
 * @desc Gestion des options du site.
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Options;

use tiFy\Contracts\Options\OptionsPageInterface;

final class Options
{
    /**
     * Liste des éléments.
     * @var OptionsPageInterface[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function () {
                foreach(config('options', []) as $name => $attrs) :
                    $this->items[$name] = app(OptionsPageInterface::class, [$name, $attrs]);
                endforeach;

                if (!isset($this->items['tify_options'])) :
                    $this->items['tify_options'] = app(OptionsPageInterface::class, ['tify_options', []]);
                endif;
            }
        );
    }
}