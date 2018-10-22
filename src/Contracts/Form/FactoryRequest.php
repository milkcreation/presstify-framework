<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Form\FactoryResolver;
use tiFy\Contracts\Kernel\ParamsBagInterface;

interface FactoryRequest extends FactoryResolver, ParamsBagInterface
{

}