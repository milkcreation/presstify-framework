<?php

namespace tiFy\Contracts\Kernel;

use \ArrayAccess;
use \Countable;
use \IteratorAggregate;
use \JsonSerializable;

interface ParametersBagIteratorInterface
    extends ArrayAccess, Countable, IteratorAggregate, JsonSerializable, ParametersBagInterface
{

}