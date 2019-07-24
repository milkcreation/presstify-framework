<?php declare(strict_types=1);

namespace tiFy\Template\Templates\UserListTable;

use tiFy\Template\Templates\ListTable\Contracts\Item as BaseItemContract;
use tiFy\Template\Templates\ListTable\Item as BaseItem;
use tiFy\Template\Templates\UserListTable\Contracts\Item as ItemContract;
use tiFy\Wordpress\Contracts\QueryUser as QueryUserContract;
use tiFy\Wordpress\Query\QueryUser;

/**
 * @mixin QueryUser
 */
class Item extends BaseItem implements ItemContract
{
    /**
     * Instance du gabarit associé.
     * @var UserListTable
     */
    protected $factory;

    /**
     * Objet de délégation d'appel des méthodes de la classe.
     * @var QueryUserContract|null
     */
    protected $delegate;

    /**
     * @inheritDoc
     */
    public function parseDelegate(): BaseItemContract
    {
        $this->delegate = QueryUser::createFromId($this->getKeyValue());

        return $this;
    }
}