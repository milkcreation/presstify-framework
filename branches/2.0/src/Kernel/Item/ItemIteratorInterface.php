<?php

namespace tiFy\Kernel\Item;

use Countable;
use ArrayAccess;
use IteratorAggregate;

interface ItemIteratorInterface extends ArrayAccess, Countable, ItemInterface, IteratorAggregate
{

}