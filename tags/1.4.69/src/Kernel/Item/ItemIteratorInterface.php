<?php

namespace tiFy\Kernel\Item;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

interface ItemIteratorInterface extends ArrayAccess, Countable, ItemInterface, IteratorAggregate, JsonSerializable
{

}