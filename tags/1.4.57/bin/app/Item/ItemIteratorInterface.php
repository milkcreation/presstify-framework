<?php

namespace tiFy\App\Item;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

interface ItemIteratorInterface extends ArrayAccess, Countable, ItemInterface, IteratorAggregate, JsonSerializable
{

}