<?php declare(strict_types=1);

namespace tiFy\Template\Templates\UserListTable;

use BadMethodCallException;
use Exception;
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
     * Objet de délégation associé.
     * @var QueryUserContract
     */
    protected $delegateObject;

    /**
     * @inheritDoc
     */
    public function __call($name, $args)
    {
        try {
            return $this->delegateObject->$name(...$args);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(__('La méthode %s n\'est pas disponible.', 'tify'), $name));
        }
    }

    /**
     * @inheritDoc
     */
    public function parse(): BaseItemContract
    {
        parent::parse();

        $this->delegateObject = QueryUser::createFromId($this->getKeyValue());

        return $this;
    }
}