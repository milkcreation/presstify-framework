<?php

namespace tiFy\Apps\Attributes;

use Countable;
use ArrayAccess;
use IteratorAggregate;

interface AttributesIteratorInterface extends ArrayAccess, AttributesControllerInterface, Countable, IteratorAggregate
{

}