<?php

declare(strict_types=1);

namespace tiFy\Contracts\Template;

use Pollen\Http\RequestInterface;

interface FactoryRequest extends FactoryAwareTrait, RequestInterface
{

}