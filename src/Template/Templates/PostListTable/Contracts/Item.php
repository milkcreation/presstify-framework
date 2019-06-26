<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable\Contracts;

use tiFy\Template\Templates\ListTable\Contracts\Item as BaseItem;
use tiFy\Wordpress\Query\QueryPost;

/**
 * @mixin QueryPost
 */
interface Item extends BaseItem
{
    /**
     * Délégation d'appel des méthodes du de l'object associé.
     *
     * @param string $name Nom de qualification de la méthode.
     * @param array $args Liste des paramètres passés en arguments à la méthode.
     *
     * @return mixed
     */
    public function __call($name, $args);
}